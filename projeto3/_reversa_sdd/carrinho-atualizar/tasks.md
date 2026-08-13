# Carrinho Atualizar (POST /carrinho/atualizar), Tarefas de Implementação

## Pré-requisitos

- [ ] Migration 9 aplicada (carts/cart_items)
- [ ] Unit `carrinho-adicionar` com `getCartByUserId`/`getCartItemsFromCookie`/`saveCartCookie` implementados

## Tarefas

- [ ] T-01, Registrar a rota `cart_update` (POST `/carrinho/atualizar`, controller `Cart/Cart`, `doUpdateCartQuantity`, sem middlewares)
  - Origem no legado: `src/Configs/routes.php:191-202`
  - Critério de pronto: POST `/carrinho/atualizar` invoca `doUpdateCartQuantity`
  - Confiança: 🟢

- [ ] T-02, Implementar `updateCartItemQuantity` (increase/decreate sobre `cart_items`, DELETE quando qtd ≤ 1 em `decrease`, mensagens de erro para carrinho/item inexistentes)
  - Origem no legado: `src/Services/Cart/CartService.php:91-151`
  - Critério de pronto: UPDATE/DELETE corretos por `id` do item
  - Confiança: 🟢

- [ ] T-03, Implementar `updateCartItemQuantityCookie` (increase/decreate sobre o cookie com remoção + `array_values` em qtd ≤ 1)
  - Origem no legado: `src/Services/Cart/CartService.php:275-298`
  - Critério de pronto: cookie regravado corretamente
  - Confiança: 🟢

- [ ] T-04, Implementar `doUpdateCartQuantity` validando `product_id` e whitelist `action`, desviando logado/cookie e redirecionando 302 `/carrinho`
  - Origem no legado: `src/Controllers/Cart/Cart.php:65-84`
  - Critério de pronto: entradas inválidas não alteram carrinho; válidas executam ação
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, `increase` logado → `quantity + 1` e 302 `/carrinho`
- [ ] TT-02, `decrease` qtd > 1 → `quantity - 1`
- [ ] TT-03, `decrease` qtd = 1 → item removido do `cart_items`
- [ ] TT-04, `increase`/`decrease` visitante → cookie atualizado
- [ ] TT-05, `action` inválido → 302 sem alteração
- [ ] TT-06, `product_id` inválido → 302 sem alteração
- [ ] TT-07, Carrinho sem o item → 302 sem alteração (falha silenciosa)

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Nenhuma (schema da migration 9 já cobre a unit)

## Ordem Sugerida

1. T-02 (serviço DB) → T-03 (serviço cookie)
2. T-01 (rota) → T-04 (controller)
3. Testes TT-01–TT-07 ao final

## Lacunas Pendentes (🔴)

- 🔴 Nenhuma humana para esta unit. (Lacunas 🟡: sem limite de estoque no `increase`; falhas silenciosas.)
