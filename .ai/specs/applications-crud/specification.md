# Spec — Applications CRUD

Behaviour/acceptance criteria for the Applications screens listed in
`.ai/features/v1.md#screens-v1`. This describes *what* must happen, not
*how* it's built — routes, controller/request/rule design, and Blade
component structure remain the human's call, per `.ai/constitution.md`'s
human/agent split.

Written against the state of the codebase on 2026-09-04: routes, the
controller, both form requests, `TransitionToStatusRule`, and
`ApplicationStatusChangeTest.php` already exist. This spec describes the
intended behaviour going forward (including for the still-empty view
files) and flags a few places where current behaviour and stated decisions
(`.ai/decisions.md`) don't yet line up — those are called out explicitly,
not silently assumed correct.

## Table of contents

- [Index](#index)
- [Show](#show)
- [Create / Store](#create--store)
- [Edit / Update](#edit--update)
- [Destroy](#destroy)
- [Status field, across create and update](#status-field-across-create-and-update)
- [Open questions / inconsistencies to resolve](#open-questions--inconsistencies-to-resolve)
- [Out of scope](#out-of-scope)

## Index

**Route:** `GET /applications` → `applications.index`

- Lists all applications, paginated.
- Each row shows at minimum: role title, company name, current status,
  `applied_at`.
- No filter/sort in v1 (per `.ai/features/v1.md#screens-v1`, "filter/sort
  TBD" — still TBD, not decided here).

## Show

**Route:** `GET /applications/{application}` → `applications.show`

- Full detail: company, contact(s) of that company, current status, the
  interaction timeline (ordered by `occurred_at`, not `created_at` — a
  backdated interaction should appear in chronological position, not at
  the end).
- A `status_change` interaction's body should be readable on its own
  (already the case — `Application::boot()` writes "Status changed from X
  to Y.").
- Not found (`{application}` doesn't exist): standard 404, no special
  handling needed.

## Create / Store

**Routes:** `GET /applications/create` → `applications.create`,
`POST /applications` → `applications.store`

Required on submit: `company_id`, `role_title`, `status`, `applied_at`.
Optional: `source`, `notes`.

- **Company: lookup-or-create.** Per
  `.ai/features/v1.md#screens-v1`: *"company lookup-or-create inline
  (`name` is the only required company field)"*. Currently
  `StoreApplicationRequest` only validates `company_id` against an
  existing company (`Rule::exists('companies', 'id')`) — there is no path
  yet for supplying a new company by name at the same time as creating the
  application. This is unimplemented, not just unspecified; see open
  questions below.
- **Status on create — resolved (2026-09-10):** not user-editable. Every
  new application starts at `ApplicationStatus::LEAD`, set server-side;
  `StoreApplicationRequest` no longer accepts or validates a `status`
  field at all. See `.ai/decisions.md`.
- `applied_at`: required, `Y-m-d` format. Whether it may be a future date
  isn't decided (same open item already flagged in
  `.ai/features/v1.md#validation`).
- On success: redirect to `applications.show` for the new application.
- On validation failure: redirect back with errors, per normal Laravel
  form conventions (not JSON — this is a Blade form, unlike the JSON
  requests currently exercised in the tests).

## Edit / Update

**Routes:** `GET /applications/{application}/edit` → `applications.edit`,
`PUT /applications/{application}` → `applications.update`

- All fields optional on update except that a submitted `status` must
  respect `ApplicationStatus::canTransitionTo()` (see below).
- On success: redirect to `applications.show`.
- On validation failure: redirect back with errors (Blade form — see the
  JSON-vs-Blade note under Create/Store; the current tests exercise this
  endpoint via `putJson`, which is fine for testing the validation rules in
  isolation but isn't the form's real submission format).

## Destroy

**Route:** `DELETE /applications/{application}` → `applications.destroy`

- Deletes the application, redirects to `applications.index`.
- **Not yet handled: its `Interaction` records.** None of the foreign keys
  (`applications.company_id`, `interactions.application_id`,
  `interactions.contact_id`, `contacts.company_id`) have
  `cascadeOnDelete()` in the migrations. Deleting an `Application` that has
  `Interaction` rows will currently either violate the FK constraint (if
  SQLite foreign key enforcement is on) or leave orphaned rows (if it's
  off). Needs a decision: cascade-delete the interactions, or block
  deletion while interactions exist, or something else. See open
  questions.

## Status field, across create and update

- Allowed transitions are entirely defined by
  `ApplicationStatus::canTransitionTo()` (`.ai/features/v1.md#status-flow`)
  — this spec doesn't repeat that table, it's the single source of truth.
- `TransitionToStatusRule` (used in `UpdateApplicationRequest` only)
  re-validates the same rule the model's `Application::transitionTo()`
  already enforces — this is deliberate, see resolved question #1 below.
- **Resolved (2026-09-10):** `ApplicationController::update()` now routes
  a submitted `status` through `Application::transitionTo()` (via
  `Request::whenEnum()`), not plain mass-assignment. Non-status fields are
  `fill()`ed first; when `status` is present, `transitionTo()`'s own
  `save()` persists everything in one write. When `status` is absent, the
  `default` callback saves the filled fields. The model is now
  self-guarding regardless of caller — see `.ai/decisions.md`.
- A validation failure on `status` must not create a `status_change`
  `Interaction` — already covered by
  `ApplicationStatusChangeTest.php`'s `'an exception is thrown when the
  application status is prohibited'` test, at the model level.

## Open questions / inconsistencies to resolve

Flagging these rather than deciding them — this is exactly the kind of
call `.ai/decisions.md` says should be made deliberately, not by default.

~~1. `ApplicationController::update()` doesn't call
`Application::transitionTo()`.~~ **Resolved 2026-09-10** — see
[Status field, across create and update](#status-field-across-create-and-update)
and `.ai/decisions.md`.

1. **Company lookup-or-create isn't built.** The screens checklist calls
   for it; `StoreApplicationRequest` doesn't support it yet
   (`company_id` must already exist). Needs its own design decision (new
   company created inline via the same form vs. a separate
   "create company first" flow) — likely belongs in a follow-up spec
   rather than being bolted onto this one.
2. **Cascade behaviour on `Application` delete** (see Destroy above) isn't
   decided.
3. ~~Status on create — should it be restricted to `lead` only, or left
   open as it is now?~~ **Resolved 2026-09-10** — see
   [Create / Store](#create--store) and `.ai/decisions.md`.
4. **JSON vs. Blade-form testing.** The existing tests hit
   `applications.update` via `putJson()` and assert JSON validation error
   structures (`assertJsonValidationErrors`). If the real form is a normal
   HTML POST (not fetch/JSON), the production error path is session-flashed
   errors, not JSON — worth having at least one test that exercises the
   actual non-JSON path, so the two don't silently diverge.

## Out of scope

- Filtering/sorting the index (per `.ai/features/v1.md`, still TBD, not
  decided here).
- Company/Contact CRUD — separate screens, separate spec.
- Anything already listed under `.ai/features/v1.md#out-of-scope-explicit`.
