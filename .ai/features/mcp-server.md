# MCP server for Application Tracker

Design/how-to document, not a v1 feature spec for the app itself. Goal: build
a local MCP server (yourself, not via an agent session — see "Role split"
below) so applications/companies/contacts/interactions can be queried
through Claude, without opening the UI.

Recorded after a short brainstorming session, 2026-09-02.

## Table of contents

- [Decisions](#decisions)
- [Role split](#role-split)
- [How to build it — step by step](#how-to-build-it--step-by-step)
- [Later extensions (deliberately out of scope now)](#later-extensions-deliberately-out-of-scope-now)
- [Separate: Laravel Boost for development conversations](#separate-laravel-boost-for-development-conversations)

## Decisions

**Goal.** Personal convenience. Not part of the portfolio showcase itself
(the quality bar from `../constitution.md` applies to the app, not
necessarily to this tool) — purely to reach the data faster yourself.

**Transport.** Local, stdio, via Claude Code. No hosting, no network, no
auth layer needed — the server runs as a separate process on the same
machine as the app, started by Claude Code itself. Deliberately not
remote/HTTP: no multiple users, no other device needed, so no OAuth flow
like the one the Taverne planning system needs
(`taverne-planning-mcp-server`).

**Package.** The official **`laravel/mcp`** (Laravel team, compatible with
Laravel 12.x/13.x — matches this project's `^13.8`). Not one of the
community alternatives (`opgginc/laravel-mcp-server`, `php-mcp/laravel`): a
first-party package fits the same "no unnecessary dependencies" discipline
as the rest of the stack.

**Scope (v1) — read-only.** No write tools. A tool that would change
`Application::status` would have to go through the same path as the UI (the
`Observer` that generates the `status_change` interaction, and
`ApplicationStatus::canTransitionTo()`) — correctly respecting that from a
standalone MCP tool is more risk than the first version is worth. Read
first; writing is a deliberate, separate, later step if reading works out.

Example tools for v1 (exact names/signatures: up to you while building):

- **List applications** — filterable by status (`lead`/`applied`/
  `interviewing`/`offer`/`rejected`/`withdrawn`), with the company name.
- **Application detail** — including the interaction timeline and the
  contacts of the associated company.
- **List companies** — with enrichment status (`enriched_at` set or not).
- **Search interactions** — e.g. "latest interactions at company X", "all
  interviews this month".

Explicitly **not** in v1: logging a new interaction, changing status,
creating/editing a company or contact. See "Later extensions".

**Data access.** Directly through the existing Eloquent models
(`Application`, `Company`, `Contact`, `Interaction`), in-process — no
separate internal API layer. One PHP process, local, single-user: an extra
HTTP hop between the MCP tool and the data adds nothing here. Reuse
existing query scopes/builders (`for...()`/`search...()`/`which...()`
prefixes, see `../constitution.md`) instead of writing new ad-hoc queries
in the tool classes. At the time of writing, the models (`Application`,
`Company`, `Contact`, `Interaction`) don't have any scopes yet — the code
examples below query directly for that reason; add scopes on the models
first if a query starts repeating across tools.

## Role split

`../../CLAUDE.md` in this project explicitly forbids an agent session from
modifying anything under `app/**` ("backend is human work"). MCP tool
classes live under `app/Mcp/...` — so **you build this yourself**, not
through a Claude Code session on this project. This document is the design
you act on yourself; no exception to the role split is made for it.

## How to build it — step by step

You have no prior experience with `laravel/mcp`, so this section walks
through the whole thing concretely, with code tailored to this app's actual
models. Treat the code as a starting point, not a copy-paste-and-done
answer — check it against the real model/enum code as you go, since this
document is a snapshot, not a contract (the models may have grown scopes or
fields by the time you build this).

### 1. Install the package

```bash
herd php artisan --version   # sanity check you're in the right project
composer require laravel/mcp
herd php artisan vendor:publish --tag=ai-routes
```

The publish command creates `../../routes/ai.php` — this is where MCP servers get
registered, parallel to `../../routes/web.php`/`routes/api.php`.

### 2. Create the server

```bash
herd php artisan make:mcp-server ApplicationTrackerServer
```

This generates `../../app/Mcp/Servers/ApplicationTrackerServer.php`:

```php
<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Application Tracker')]
#[Version('1.0.0')]
#[Instructions('Read-only access to job applications, companies, contacts, and interactions.')]
final class ApplicationTrackerServer extends Server
{
    protected array $tools = [];

    protected array $resources = [];

    protected array $prompts = [];
}
```

(Add `declare(strict_types=1)` and `final` yourself — the generator doesn't
add those; keep it consistent with `../constitution.md`.)

### 3. Register the server as local/stdio

In `../../routes/ai.php`:

```php
<?php

declare(strict_types=1);

use App\Mcp\Servers\ApplicationTrackerServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('application-tracker', ApplicationTrackerServer::class);
```

`Mcp::local()` is the stdio variant (a CLI process Claude Code starts and
talks to over stdin/stdout) — this is what you want here, as opposed to
`Mcp::web()`, which registers an HTTP endpoint and needs auth middleware
(not needed for this project, see "Decisions" above).

### 4. Create your first tool

```bash
herd php artisan make:mcp-tool ListApplicationsTool
```

Generates `../../app/Mcp/Tools/ListApplicationsTool.php`. Filled in for this
app's data model:

```php
<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Lists job applications, optionally filtered by status.')]
final class ListApplicationsTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()
                ->enum(array_map(fn (ApplicationStatus $s): string => $s->value, ApplicationStatus::cases()))
                ->description('Filter by application status. Omit to list all.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'status' => 'nullable|string|in:' . implode(',', array_map(
                fn (ApplicationStatus $s): string => $s->value,
                ApplicationStatus::cases(),
            )),
        ]);

        $applications = Application::query()
            ->with('company')
            ->when(
                $validated['status'] ?? null,
                fn ($query, string $status) => $query->where('status', $status),
            )
            ->orderByDesc('applied_at')
            ->get();

        $lines = $applications
            ->map(fn (Application $application): string => sprintf(
                '#%d — %s at %s (%s)',
                $application->id,
                $application->role_title,
                $application->company->name,
                $application->status->value,
            ))
            ->implode("\n");

        return Response::text($lines !== '' ? $lines : 'No applications found.');
    }
}
```

Points worth noting, since these are easy to get wrong on a first tool:

- `schema()` describes the *input* the tool accepts — it is validated again
  in `handle()` via `$request->validate()`, same as a Form Request. The
  schema is what the AI client sees; the `validate()` call is what actually
  protects you.
- `Response::text()` is the simplest return type — a plain string. Use
  `Response::structured()` instead if you want the result to be
  machine-parseable JSON rather than a formatted string (worth doing once
  you have more than one or two tools returning lists).
- Follow the same model conventions as the rest of the app: reuse
  `for...()`/`search...()` query scopes once they exist instead of
  building `where()` chains ad hoc in the tool.

### 5. Register the tool on the server

Back in `../../app/Mcp/Servers/ApplicationTrackerServer.php`:

```php
use App\Mcp\Tools\ListApplicationsTool;

protected array $tools = [
    ListApplicationsTool::class,
];
```

Repeat steps 4–5 for the other v1 tools (application detail, list
companies, search interactions).

### 6. Test the tool with Pest

The package ships test helpers so you don't need a running MCP client to
verify a tool:

```php
<?php

declare(strict_types=1);

use App\Mcp\Servers\ApplicationTrackerServer;
use App\Mcp\Tools\ListApplicationsTool;

test('lists applications filtered by status', function (): void {
    // Arrange: create applications via factories, some `applied`, some `interviewing`.

    $response = ApplicationTrackerServer::tool(ListApplicationsTool::class, [
        'status' => 'applied',
    ]);

    $response
        ->assertOk()
        ->assertSee('applied');
});
```

Run it the normal way (`herd php artisan test` or `../../vendor/bin/pest`) — this
is a regular Pest test, no MCP client needed. Write this test **before**
the tool implementation, per the project's usual TDD discipline.

### 7. Try it interactively with the MCP Inspector

Before wiring it into Claude Code, sanity-check it stand-alone:

```bash
herd php artisan mcp:inspector application-tracker
```

This opens an interactive session where you can call each tool by hand and
see the raw response — the fastest way to catch a schema mistake.

### 8. Connect it to Claude Code

Claude Code (the CLI, what you're using now) is configured per-project via
`claude mcp add`, not via the Claude Desktop config file (that file is only
for the separate Desktop app). From the `application-tracker` project root:

```bash
claude mcp add --scope project --transport stdio application-tracker \
  -- herd php artisan mcp:start application-tracker
```

- `--scope project` writes the entry to `../../.mcp.json` in the repo root, so it
  travels with the project instead of only existing on your machine's
  global Claude config. Commit `../../.mcp.json` — it contains the launch
  command, not a secret.
- `herd php` matches your existing convention for running PHP (see your
  global `../../CLAUDE.md`) rather than a bare `php`, so it resolves to the
  version Herd manages for this project.
- After adding it, restart Claude Code (or start a new session) in this
  project; the tools become available automatically — no manual JSON
  editing needed for Claude Code specifically (unlike Claude Desktop).

### 9. Iterate

Add the remaining v1 tools the same way (steps 4–6), keep each tool's
`handle()` small and focused on one query, and lean on the Inspector
(step 7) whenever a tool's output looks wrong before assuming the AI client
is misusing it.

## Later extensions (deliberately out of scope now)

- Write tools (log an interaction, change status) — only once the read
  tools have proven themselves, and with explicit attention to respecting
  the Observer/`canTransitionTo()` rules.
- Remote/HTTP transport, if there's ever a need to work from another
  device — would then still require an auth decision.
- CRUD on companies/contacts — largest scope, smallest first step, so
  deliberately deferred.

## Separate: Laravel Boost for development conversations

Different purpose from the MCP server above — not for querying application
data, but for making Claude more useful **while you and I talk about the
backend code** (this project's `../../CLAUDE.md` already restricts me to
`resources/views/**`, but that doesn't stop you from discussing `app/**`
with me — it just means I can't edit it). `laravel/boost` ships a
ready-made MCP server with schema inspection, a Tinker tool, a database
query tool, and semantic search over version-specific Laravel
documentation — useful for grounding those conversations instead of me
reading files or guessing at Laravel 13 behaviour.

Two things to keep in mind, independent of the `app/**` edit restriction
above:

- The database query tool is read-only by default, but **Tinker itself can
  mutate** — it's a full REPL. Low risk here (local SQLite dev database),
  but not a hard read-only guarantee the way the query tool is.
- This adds an **execution channel** (running Tinker, running queries)
  that's new compared to today — a capability, not just a read, separate
  from the existing "no file edits under `app/**`" rule.

Install:

```bash
composer require laravel/boost --dev
herd php artisan boost:install
```

The installer detects Claude Code automatically and writes the MCP config
plus the Laravel guideline files.
