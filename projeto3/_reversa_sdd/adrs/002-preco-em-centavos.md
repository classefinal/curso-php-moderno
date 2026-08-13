# ADR-002 — Preço armazenado em centavos (INT)

- **Status:** Aceito 🟢
- **Data:** 2026-08-12 (retroativo)
- **Origem:** schema `products.price` INT; formatação em `Cart/CartService`, `CartItem` types

## Contexto

Cálculos com float (REAL/DECIMAL nativo do PHP) acumulam erros de arredondamento em soma de carrinho e exibição.

## Decisão

- `products.price` é `INT` representando **centavos**.
- Total do carrinho calculado inteiramente em centavos (`price * quantity`).
- Formatação apenas na exibição: `number_format($total/100, 2, ',', '.')` → `R$ 1.234,56` (pt-BR).

## Consequências

- Somas e produtos aritméticos exatos entre inteiros.
- Qualquer código que insira/leia preço precisa converter centavos ↔ reais.
- 🟡 Risco: escrita manual no banco (ex.: `99.99`) fica inconsistente com a convenção.
