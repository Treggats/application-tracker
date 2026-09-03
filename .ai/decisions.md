# Decisions

Non-trivial design choices with rationale. Only add an entry if the "why"
can't be derived from the code, `CLAUDE.md`, or `.ai/constitution.md`
itself. Newest on top. Format per decision: short title, date, **Why**,
optionally **Alternative considered**.

Not tied to the `.ai/council.md` process: a decision can land here without
a council session, as long as the rationale is worth keeping. See
`.ai/council/` for decisions that did go through a council deliberation —
that's where the trade-off lives; this is where the outcome does.

---

## Status transitions: forward-only, terminal-final; reapplying creates a new `Application`
_Recorded: 2026-09-01_

The v1 spec listed the status flow (`lead` → `applied` → `interviewing` →
terminal: `offer` | `rejected` | `withdrawn`) but not whether a status could
move backward, nor where the legality of a transition gets checked.
`ApplicationStatus::statusChange()` was mid-implementation and didn't
handle the branch at `interviewing` at all.

**Why:** no backward transitions — a terminal status is final. If a
rejected/withdrawn application turns into a new opportunity (reapplying, or
the company reaching out again), that's a **new `Application` record**, not
a revived status on the old one. Keeps the history honest: one record, one
linear-then-terminal path, no ambiguity about "was this reopened or is this
a fresh attempt".

Transition legality is validated on the enum itself —
`ApplicationStatus::canTransitionTo(self $target): bool` — rather than on
the `Application` model or a form request. Keeps the allowed-transitions
table next to the enum it describes; the Observer still owns turning an
accepted write into an `Interaction`, it doesn't decide legality.

See `.ai/features/v1.md#status-flow`.

---

## `applied` can also go straight to `rejected`/`withdrawn`, without an interview
_Recorded: 2026-09-02_

The status flow originally only let the three terminal statuses branch off
`interviewing`. In practice a rejection or a withdrawal can happen right
after applying, before an interview is ever scheduled — the flow shouldn't
force an application through `interviewing` just to reach a terminal state
it never earned.

**Why:** `ApplicationStatus::canTransitionTo()` now allows
`applied → interviewing|rejected|withdrawn`, not just `applied →
interviewing`. `offer` is deliberately not part of that set — an offer
without an interview isn't a realistic v1 case, so `applied → offer` stays
disallowed. See `.ai/features/v1.md#status-flow`.

---

## Companies get a standalone index screen
_Recorded: 2026-09-01_

The original spec didn't say whether a company was reachable only via an
application, or had its own list view.

**Why:** v1 gets a standalone Companies index. See
`.ai/features/v1.md#screens-v1`.

---

## Status changes go through one model method; mass updates stay a known gap
_Recorded: 2026-09-03_

The `status_change` `Interaction` is created by a model observer reacting
to `updated`/`wasChanged('status')`. Eloquent mass updates
(`Application::query()->update(['status' => ...])`) never fire model
events, so a mass update would silently desync `status` from the
interaction history — the exact class of bug the observer exists to
prevent.

**Why:** rather than adding architectural enforcement (a custom PHPStan
rule, a Pest arch test) disproportionate to a single-user, non-production
app, status changes go through one model method
(`Application::transitionTo()`, wrapping `canTransitionTo()` + `save()`) as
the sole convention — no other code path exists in the codebase for it, so
a mass update on `status` would be a visible, deliberate departure at
review time, not an easy accidental default. This is a recorded, accepted
gap, not a solved one: nothing currently stops a mass update from being
written. Revisit if the app ever gets a second write path to `status`
(e.g. a bulk action in the UI).

**Alternative considered:** a custom PHPStan rule or Pest arch test
forbidding `Builder::update()` calls that touch `status` outside the model.
Rejected as more tooling than the actual risk (single author, single
codebase) justifies right now.

---

## Status-change observer lives inline on `Application`, not a separate `Observer` class
_Recorded: 2026-09-03_

Laravel supports both a dedicated `Observer` class (registered via
`#[ObservedBy]` or in a service provider) and reacting to model events
directly inside the model's own `boot()`. The spec (`v1.md`) only specifies
the *effect* ("via an observer on `Application`"), not which of the two.

**Why:** kept inline in `Application::boot()` rather than split into
`app/Observers/ApplicationObserver.php`. A separate Observer class is easy
to forget exists — it's registered elsewhere, invisible from the model
itself, and its file naturally goes unopened while working on the model.
Inline keeps the status-change side effect visible right next to `status`'s
own logic (`canTransitionTo()`), at the cost of a slightly larger model
class.

**Alternative considered:** a dedicated `ApplicationObserver` class,
Laravel's more common convention for this. Rejected for now on the
forgettability argument above; revisit if `Application` accumulates enough
inline event handling to outweigh that.

---

## Route names drop the HTTP-verb prefix, use standard Laravel resource naming
_Recorded: 2026-09-03_

`.ai/constitution.md` originally specified route names as kebab-case with
the HTTP verb first (`get.applications.index`). Dropped starting with the
applications CRUD work.

**Why:** the verb prefix duplicates information Laravel already encodes
elsewhere (the route's HTTP method, visible in `routes/web.php` and
`php artisan route:list`) without adding anything route *names* are
actually used for — generating URLs and redirects, where the verb is
irrelevant. Standard resource naming (`applications.index`,
`applications.store`, ...) is what `Route::resource()` generates by
default and what any Laravel developer already expects; the custom prefix
added friction without a matching benefit. `.ai/constitution.md` updated
directly rather than only recorded as an exception here, since this is the
project's rule itself changing, not a one-off deviation from it.

---
