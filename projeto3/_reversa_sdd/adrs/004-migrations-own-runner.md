# ADR-004 — Sistema próprio de migrations com queries tipadas

- **Status:** Aceito 🟢
- **Data:** 2026-08-12 (retroativo — commits `8d36eb8` "feat: add migration system" e `433f6fe`/`3ab1ea4`)
- **Origem:** `migrate.php`, `src/Migrations/`, `src/Services/DB.php`

## Contexto

Evolução de schema precisava de controle de histórico. O projeto não usa biblioteca externa de migrations.

## Decisão

- Runner CLI `migrate.php` executa migrations pendentes em ordem numérica do nome (`1_`, `2_`, …, `10_`).
- Cada migration é um array `['up' => closure(mysqli $connection)]` (tipo `Migration` em `types.php`).
- Tabela `migrations` (`name`, `executed`, `created_at`) registra o histórico; só marca `executed=1` se o `up` rodar sem erro.
- DDL via `dbExecuteStm` (`mysqli_query`); DML via `dbPrepareAndExecute` com parâmetros tipados `['type' => 's'|'i', 'value' => $val]`.

## Consequências

- Migrations idempotentes por registro (não reexecutam) mas **não há down()/rollback**.
- Formato tipado documenta intenção dos parâmetros (bom para IDE via Psalm).
- 🔴 Migrations atuais **não refletem o schema real do banco** (ver ADR-008/009).
