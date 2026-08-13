# ADR-006 — Carrinho dual: banco (logado) e cookie (visitante)

- **Status:** Aceito 🟢
- **Data:** 2026-08-12 (retroativo — commit `cba7f46` "wip: add cart and about")
- **Origem:** `src/Services/Cart/CartService.php`, `Pages/Cart/cart.php`

## Contexto

Visitantes não autenticados precisavam usar o carrinho sem criar conta; usuários logados precisavam de persistência entre dispositivos.

## Decisão

- **Usuário logado** → carrinho no banco: `carts` com `UNIQUE user_id` (1 carrinho por usuário) + `cart_items` (`quantity`).
- **Visitante** → cookie `cart_items` (`id:qty,id:qty`, validade **30 dias**), enriquecido apenas com produtos ativos.
- Operações comuns às duas fontes: adicionar (incrementa), aumentar, diminuir (remove em qty ≤ 1), remover.
- Decisão de fonte feita por `isset($_SESSION['user'])` em cada controller de carrinho.

## Consequências

- Nenhum fluxo de **migração cookie→banco** no login (itens do visitante não são absorvidos ao logar 🟡).
- Lógica duplicada (banco/cookie) nos services de adicionar/atualizar/remover.
- Sem estoque máximo validado: `quantity` pode exceder `products.stock` 🟡.
