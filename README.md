# Application Tracker

A single-user tool to track job applications, companies, contacts, and
interactions during a job search. Personal use, not a production service —
also built as a portfolio piece demonstrating code discipline end to end.

## Stack

- PHP 8.4, Laravel (latest stable major)
- SQLite
- Pest for tests
- Plain Blade components (no `@extends`/`@section`/`@yield`/`@parent`)
- No JS framework

## Getting started

```bash
composer setup
composer dev
```

`composer setup` installs dependencies, copies `.env.example` to `.env`,
generates an app key, and runs migrations. `composer dev` runs the app
server, queue listener, log viewer, and Vite dev server concurrently.

## Quality gates

Every commit must stay green on:

- Pint (code style)
- PHPStan (via Larastan), highest level that stays clean
- Pest (tests)

GitHub Actions runs all three on every push/PR to `main`.

## Project docs

- [`.ai/constitution.md`](.ai/constitution.md) — project-wide rules: stack,
  code style, quality gates, scope, and the human/agent split
- [`.ai/features/`](.ai/features) — feature specs (data model, behaviour,
  scope) for each version

## Status

v1 core (applications/companies/contacts/interactions) is in development —
see `.ai/features/v1.md` for the current scope and definition of done.
