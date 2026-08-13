# Projeto3 — Project Documentation

This is a custom **procedural PHP framework** (no OOP) built as an educational project. It simulates an online store with a public product catalog, user authentication, and admin area.

## Quick Reference

| What | Where |
|------|-------|
| Entry point | `public/index.php` → `app.php` |
| Routes | `src/Configs/routes.php` (14 routes) |
| Controllers | `src/Controllers/` (7 files) |
| Services | `src/Services/` (infrastructure + business logic) |
| Templates | `src/Pages/` (7 files) |
| Components | `src/Components/` (13 reusable partials) |
| Events | `src/Configs/events.php` (2 events) |
| Listeners | `src/Listeners/` (2 files) |
| Middlewares | `src/Middlewares/` (1 middleware) |
| Migrations | `src/Migrations/` + `migrate.php` (8 migrations) |
| Type definitions | `types.php` (Psalm annotations) |
| CLI | `php migrate.php` (run pending migrations) |

## Documentation Files

| File | Covers |
|------|--------|
| [doc/ARCHITECTURE.md](doc/ARCHITECTURE.md) | Overall request flow, directory structure, core concepts |
| [doc/ROUTING.md](doc/ROUTING.md) | Route definition format, resolution, URI handling |
| [doc/CONTROLLERS.md](doc/CONTROLLERS.md) | Controller files, conventions, patterns |
| [doc/SERVICES.md](doc/SERVICES.md) | Infrastructure & business services |
| [doc/PAGES.md](doc/PAGES.md) | View templates, variable injection |
| [doc/COMPONENTS.md](doc/COMPONENTS.md) | Reusable partials (layout, product, auth) |
| [doc/CONFIGS.md](doc/CONFIGS.md) | Route and event configuration |
| [doc/EVENTS.md](doc/EVENTS.md) | Event dispatcher, available events |
| [doc/LISTENERS.md](doc/LISTENERS.md) | Listener functions, deferred execution |
| [doc/MIDDLEWARES.md](doc/MIDDLEWARES.md) | Middleware pipeline, conventions |
| [doc/MIGRATIONS.md](doc/MIGRATIONS.md) | Migration runner, format, history |
| [doc/DATABASE.md](doc/DATABASE.md) | DB connection, query functions, schema |
| [doc/FUNCTIONS.md](doc/FUNCTIONS.md) | Shared utility functions, path helpers |
| [doc/FRONTEND.md](doc/FRONTEND.md) | Assets, templating, response strategy |

## Key Technical Decisions

- **No OOP**: global functions + closures + associative arrays
- **No dependencies**: vanilla PHP 8.1+, Bootstrap 5 CDN-less, Font Awesome
- **Custom view system**: `extract()` + output buffering
- **Custom router**: regex + string matching, GET/POST on same URL
- **Custom event system**: closure-based, supports inline and file-based listeners
- **Deferred execution**: post-response actions via defer/dispatcher pattern
- **Typed DB queries**: `dbPrepareAndExecute()` with `['type' => 's'/'i', 'value' => $val]` format
- **Session auth**: `$_SESSION['user']` / `$_SESSION['admin']` checked inline
- **Psalm types**: all data shapes documented in `types.php` for IDE support


---

# Reversa

> Framework de Engenharia Reversa instalado neste projeto.

## Como usar

Use o fluxo adequado no chat:

- `reversa` — descobrir e documentar um sistema existente
- `reversa-new` — criar PRD e specs para um projeto novo
- `reversa-forward` — implementar ou evoluir código a partir das specs
- `reversa-migrate` — planejar a migração de um sistema legado
- `reversa-docs` — gerar o mini-site visual da documentação
- `reversa-agents-help` — consultar o catálogo completo de agentes

## Comportamento ao ativar

Quando o usuário digitar `reversa` sozinho em uma mensagem:

1. Ative o skill `reversa` disponível em `.agents/skills/reversa/SKILL.md`
2. Leia o SKILL.md na íntegra e siga exatamente as instruções do Reversa

## Regra não-negociável

Nunca apague, modifique ou sobrescreva arquivos pré-existentes do projeto legado.
O Reversa escreve apenas em `.reversa/`, `_reversa_sdd/`, `_reversa_docs/` e `_reversa_forward/`.
