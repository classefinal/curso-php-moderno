# ADR-008 — Regressão: migration 8 removeu email/password de users

- **Status:** Aceito (regressão documentada) 🔴
- **Data:** 2026-08-12 (retroativo — commit `511ca81` "wip: changed table", 2026-03-26)
- **Origem:** `git show 511ca81 -- src/Migrations/8_create_users_table.php`

## Contexto

A migration 8 original criava `users` com `id, name, email UNIQUE, password, active, admin, created_at`. O commit `511ca81` a reescreveu para `id, name, active, admin, created_at, updated_at` — **removendo `email` e `password`** — mas **manteve** o `INSERT INTO users (name, email, password, active, admin)` com `admin@admin.com` / `admin123`.

## Decisão (observada, não intencional)

Aparentemente a "simplificação" do schema quebrou a tabela. O código de login/perfil (`LoginService`, `UsersService`, `profile.php`) continua dependendo de `email` e `password`.

## Consequências

- 🔴 **LACUNA CRÍTICA:** em banco limpo, a migration 8 falha (`INSERT` referencia colunas inexistentes) ou o banco real foi corrigido manualmente.
- `types.php` (`User`) não declara `email`, evidenciando que a mudança não foi propagada.
- Qualquer reexecução do schema do zero não reproduz o sistema funcional.
- Correção sugerida: restaurar as colunas `email VARCHAR(255) UNIQUE` e `password VARCHAR(255)` na migration 8.
