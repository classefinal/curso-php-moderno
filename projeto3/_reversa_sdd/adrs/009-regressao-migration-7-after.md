# ADR-009 — Regressão: migration 7 com AFTER autorreferente

- **Status:** Aceito (regressão documentada) 🔴
- **Data:** 2026-08-12 (retroativo — commit `20210e4` "wip: fixed some types", 2026-03-19)
- **Origem:** `git show 20210e4 -- src/Migrations/7_add_product_short_description.php`

## Contexto

A migration 7 original (commit `40357d5`, 2026-03-18) era correta:

```sql
ALTER TABLE products ADD short_description VARCHAR(255) NOT NULL DEFAULT '',
                   ADD description_line VARCHAR(150) NOT NULL DEFAULT '';
```

O commit `20210e4` a trocou para:

```sql
ALTER TABLE products ADD short_description VARCHAR(255) NOT NULL DEFAULT '' AFTER description_line,
                   ADD description_line VARCHAR(150) NOT NULL DEFAULT '' AFTER short_description;
```

Cada coluna referencia a **outra** como posição `AFTER`, num único comando.

## Decisão (observada, não intencional)

Tentativa de "arrumar tipos" que introduziu dependência circular de posição. MySQL tende a rejeitar `ADD ... AFTER <coluna-inexistente>` no mesmo ALTER.

## Consequências

- 🔴 **LACUNA:** em banco limpo, a migration 7 tende a falhar; o schema real deve ter sido corrigido manualmente.
- `short_description` e `description_line` existem no código (Product types, páginas, cart) — o banco de verdade as possui de alguma forma.
- Correção sugerida: voltar ao ALTER sem cláusulas `AFTER`.
