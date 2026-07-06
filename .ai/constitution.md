# Constitution — Applications Tracker

Project-wide rules. Feature specs (`.ai/features/*.md`) may tighten these rules, never contradict them.

## Purpose

A single-user tool to track job applications, companies, contacts, and interactions. Personal use, not a production service. The project also serves as a portfolio proof piece: the architecture and git history must demonstrate the code discipline claimed elsewhere.

## Stack

- PHP 8.5, Laravel (latest stable major)
- SQLite
- Pest for tests
- Plain Blade with Blade components (no `@extends`/`@section`/`@yield`/`@parent`)
- No JS framework (no Vue/React/Livewire in v1)

## Code style (hihaho guidelines)

- PSR-12 as the base
- `declare(strict_types=1)` in every file
- Classes `final` by default, methods `private` by default
- Always return types, including `void` and on closures
- Happy path last, avoid `else`, use early returns
- Space after `!`: `if (! $foo)`
- String interpolation over concatenation
- No docblocks when type hints suffice
- Query builders via `Model::query()->...`, chaining on new lines
- Eloquent Builder aliased as `EloquentQueryBuilder`
- Custom query builders with `for...()`, `search...()`, `which...()` prefixes
- Route names kebab-case with dot notation, HTTP verb first (e.g. `get.applications.index`)
- Config keys snake_case, config filenames kebab-case

Note: `private`-by-default applies to your own methods, not to framework contact points. Controller actions and component `render()` methods must be `public` because Laravel calls them.

## Quality gates (non-negotiable)

Every PR/commit series must be green on:

- Pint (code style)
- PHPStan at the highest level that stays clean
- Pest (tests)
- GitHub Actions runs all three

This discipline applies from commit one. Not "clean up later" — the git history is part of the proof.

## Scope discipline

Every feature spec contains an explicit "out of scope" section. Good ideas the core does not need go to a later version, not to v1.

Roadmap:
- **v1** — core (applications/companies/contacts/interactions) + optional KVK enrichment behind an interface
- **v2** — tags/skills per role + filtering
- **v3** — vacancy integration

## Human/agent split

The backend is human work. An agent (Claude Code) may only fill in the presentation layer within a frozen contract.

- **Human writes**: everything under `app/`, routes, controllers, form requests, models, enums, services, migrations, component classes (class-based Blade components), and the component signatures (`@props` + types).
- **Agent may only touch**: markup within `resources/views` (anonymous Blade components) and frontend assets.
- **Agent may never**: modify routes, controllers, form requests, models, or anything under `app/`; invent props; use `@extends`/`@section`/`@yield`/`@parent`.

Reason: the architectural decisions must be demonstrably the human's. Nobody cares about the markup; the architecture matters.
