# Carrinho Remover (POST /carrinho/remover), Tarefas de Implementação

## Pré-requisitos

- [ ] Migration 9 aplicada (carts/cart_items)
- [ ] Unit `carrinho-adicionar` com `getCartByUserId`/`getCartItemsFromCookie`/`saveCartCookie` implementados

## Tarefas

- [ ] T-01, Registrar a rota `cart_remove` (POST `/carrinho/remover`, controller `Cart/Cart`, `doRemoveCartItem`, sem middlewares)
  - Origem no legado: `src/Configs/routes.php:203-214`
  - Critério de pronto: POST `/carrinho/remover` invoca `doRemoveCartItem`
  - Confiança: 🟢

- [ ] T-02, Implementar `removeCartItem` (`DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?`, erro de carrinho inexistente)
  - Origem no legado: `src/Services/Cart/CartService.php:153-171`
  - Critério de pronto: item removido por completo; carrinho inexistente retorna erro
  - Confiança: 🟢

- [ ] T-03, Implementar `removeCartItemCookie` (unset + `array_values` + `saveCartCookie`)
  - Origem no legado: `src/Services/Cart/CartService.php:300-315`
  - Critério de pronto: par removido do cookie; regrava se ausente
  - Confiança: 🟢

- [ ] T-04, Implementar `doRemoveCartItem` validando `product_id`, desviando logado/cookie e redirecionando 302 `/carrinho`
  - Origem no legado: `src/Controllers/Cart/Cart.php:86-104`
  - Critério de pronto: id inválido não altera nada; válido remove
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Remover item logado → linha apagada de `cart_items` e 302 `/carrinho`
- [ ] TT-02, Remover item com quantidade > 1 → removido por completo (não decrementa)
- [ ] TT-03, Remover visitante → par removido do cookie
- [ ] TT-04, `product_id` inválido → 302 sem alteração
- [ ] TT-05, Item inexistente → 302 sem erro (falha silenciosa)
- [ ] TT-06, Carrinho inexistente (DB) → 302 sem erro

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Nenhuma (schema da migration 9 já cobre a unit)

## Ordem Sugerida

1. T-02 (serviço DB) → T-03 (serviço cookie)
2. T-01 (rota) → T-04 (controller)
3. Testes TT-01–TT-06 ao final

## Lacunas Pendentes (🔴)

- 🔴 Nenhuma humana para esta unit. (Lacuna 🟡: falhas silenciosas sem feedback.)
