---
paths:
  - 'app/Models/*.php'
---

# Models

## Never mass-update through a model — Eloquent events won't fire
Eloquent mass updates (`Model::query()->update([...])`, `where(...)->update([...])`) never fire `saving`/`updating`/`updated`/`saved` events. Any model that relies on an observer/event listener to keep derived state in sync (e.g. Application's status-change Interaction) will silently desync if a mass update touches that column instead of going through a single retrieved model + save(). Always fetch-then-save (or a dedicated model method) for any attribute an observer reacts to. See .ai/decisions.md "Status changes go through one model method; mass updates stay a known gap".
