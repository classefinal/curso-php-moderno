# Data Delta: Correções da Revisão (P1–P14)

> Identificador: `001-correcoes-revisao`
> Data: `2026-08-13`
> Modelo extraído em: `_reversa_sdd/erd-complete.md`, `_reversa_sdd/data-dictionary.md`

## 1. Resumo

Nenhuma mudança de dados em runtime. As duas migrations corrigidas passam a **reproduzir o schema real** que o código já assume:

| Local | Antes (bug) | Depois (correto) |
|-------|-------------|------------------|
| `src/Migrations/8_create_users_table.php` | `CREATE TABLE users` sem `email`/`password`, mas o INSERT do seed os referencia (ADR-008) | DDL declara `email` (VARCHAR, UNIQUE) e `password` (VARCHAR) antes do INSERT |
| `src/Migrations/7_add_product_short_description.php` | `ALTER TABLE products ADD short_description ... AFTER description_line, ADD description_line ... AFTER short_description` — colunas cruzadas ainda inexistentes (ADR-009) | Colunas adicionadas sem cláusulas `AFTER` |

## 2. Detalhe por tabela

### `users` (migration 8)

- **Colunas do CREATE TABLE após a correção:** `id`, `name`, `email` (UNIQUE), `password`, `active`, `admin`, `created_at`, `updated_at` — alinhado ao `data-dictionary.md` e ao uso real (`login`, `perfil`, seed).
- **Seed** (`admin@admin.com` / bcrypt cost 16): permanece (decisão P4) — só passa a funcionar porque as colunas agora existem no DDL.
- **Tipo sugerido:** `email VARCHAR(255) NOT NULL UNIQUE`, `password VARCHAR(255) NOT NULL` (compatível com hashes bcrypt).

### `products` (migration 7)

- **Colunas:** `short_description` e `description_line` continuam existindo (confirmado por uso nos SELECTs de produto/carrinho — P2).
- **Correção:** remover as duas cláusulas `AFTER` da instrução `ALTER`. A ordem física das colunas fica a cargo do MySQL.

## 3. Impacto em ambientes

| Ambiente | Efeito |
|----------|--------|
| Banco limpo (`php migrate.php` do zero) | Aplica 10/10; `users` nasce com `email`/`password`; migration 7 não falha |
| Banco já migrado | **Nenhum** — runner marca `migrations.executed` por nome de arquivo sem checksum (`state-machines.md#Migration`); a correção do arquivo não reexecuta |
| Dados existentes | Nenhum dado é alterado, inserido ou removido |

## 4. Migrations pendentes

Nenhuma migration nova é criada. O `data-delta` é inteiramente a correção dos DDLs 7 e 8.

## 5. Risco residual

Se um ambiente tiver aplicado a migration 8 **original** (com o DDL sem `email`/`password`) e o banco tiver sido corrigido à mão, o arquivo corrigido passa a divergir do histórico local — sem impacto operacional, pois o runner não compara conteúdo.

## 6. Histórico de alterações

| Data | Alteração | Autor |
|------|-----------|-------|
| 2026-08-13 | Versão inicial gerada por `/reversa-plan` | reversa |
