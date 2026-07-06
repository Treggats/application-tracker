# CLAUDE.md

Instructions for Claude Code on this project. Also read `.ai/constitution.md` and the relevant `.ai/features/*.md`.

## Role split — hard

The backend is human work. You only fill in the presentation layer.

**You may only modify:**
- `resources/views/**` (anonymous Blade components, markup)
- frontend assets (`resources/css`, `resources/js` insofar as pure styling/behaviour)

**You may NEVER modify:**
- Anything under `app/**` (models, enums, services, controllers, form requests, component classes)
- `routes/**`
- migrations
- config

**You may NEVER:**
- Invent or add props. Use only the props the human has defined in the component signature (`@props`). Missing data? Report it, don't invent it.
- Assume routes, controller methods, or their signatures. Ask for the frozen contract.

## Blade — required and forbidden

- **Only** Blade components: `<x-...>`, `@props`, slots.
- **Forbidden**: `@extends`, `@section`, `@yield`, `@parent`. No exceptions. (This is the known fallback; use components with slots instead of template inheritance.)

## Code style (also in Blade/PHP snippets)

- `declare(strict_types=1)` in every PHP file
- Classes `final`, methods `private` unless otherwise needed
- Always return types
- Early returns, happy path last, no `else`
- Space after `!`: `if (! $foo)`
- String interpolation over concatenation

## Way of working

- For Laravel questions, consult https://laravel.com/for/agents first.
- Stay within the v1 scope from the feature spec. See something out of scope? Report it as a suggestion, don't build it.
- When in doubt about the contract: stop and ask, don't guess.
