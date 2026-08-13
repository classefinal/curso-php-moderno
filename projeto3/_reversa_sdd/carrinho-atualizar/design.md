# Carrinho Atualizar (POST /carrinho/atualizar), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| POST | `/carrinho/atualizar` | `product_id`, `action` (form-urlencoded) | redirect `Location: /carrinho` | 302 |
| POST | `/carrinho/atualizar` (entrada inválida) | `product_id`/`action` inválidos | redirect `Location: /carrinho` | 302 |
| POST | `/carrinho/atualizar` (falha de banco, logado) | UPDATE/DELETE sem efeito | redirect `Location: /carrinho` com flash de erro | 302 |

## Fluxo Principal (logado)

1. `doUpdateCartQuantity` valida `product_id` (`FILTER_VALIDATE_INT`, `min_range => 1`) e `action` (`in_array($action, ['increase', 'decrease'])`); inválido → 302 `/carrinho`. `src/Controllers/Cart/Cart.php:67-75`
2. `updateCartItemQuantity($connection, $userId, $productId, $action)`. `src/Controllers/Cart/Cart.php:78`
3. `getCartByUserId`; sem carrinho → `['success' => false, 'error' => 'Carrinho não encontrado']`. `src/Services/Cart/CartService.php:93-97`
4. `SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ? LIMIT 1`; sem item → `['success' => false, 'error' => 'Item não encontrado']`. `src/Services/Cart/CartService.php:99-110`
5. `increase` → `UPDATE cart_items SET quantity = quantity + 1 WHERE id = ?`; retorna `incremented`. `src/Services/Cart/CartService.php:114-124`
6. `decrease` com `quantity <= 1` → `DELETE FROM cart_items WHERE id = ?`; retorna `removed`. `src/Services/Cart/CartService.php:126-137`
7. `decrease` com `quantity > 1` → `UPDATE cart_items SET quantity = quantity - 1 WHERE id = ?`; retorna `decremented`. `src/Services/Cart/CartService.php:139-148`
8. Em falha de banco (UPDATE/DELETE sem efeito) → flash de erro; caso contrário 302 `/carrinho` (P10). `src/Controllers/Cart/Cart.php:83`

## Fluxo Principal (visitante)

1. Validações idênticas no controller. `src/Controllers/Cart/Cart.php:67-75`
2. `updateCartItemQuantityCookie($productId, $action)`: lê cookie, encontra o produto. `src/Services/Cart/CartService.php:275-280`
3. `increase` → `quantity++`. `src/Services/Cart/CartService.php:281-283`
4. `decrease` com `quantity <= 1` → `unset($items[$key])` + `array_values`. `src/Services/Cart/CartService.php:283-287`
5. `decrease` com `quantity > 1` → `quantity--`. `src/Services/Cart/CartService.php:288-289`
6. `saveCartCookie($items)` (setcookie 30 dias). `src/Services/Cart/CartService.php:295`
7. `$configs['redirect']('/carrinho', 302)`. `src/Controllers/Cart/Cart.php:83`

## Fluxos Alternativos

- **Item/carrinho inexistente (DB):** serviço retorna erro; controller redireciona 302 `/carrinho` **com flash de erro** (P10 — feedback para falhas de banco).
- **Cookie sem o produto:** o `foreach` não encontra → `saveCartCookie` regrava o cookie como estava, **sem feedback** (P10 — cookie é mero indicador de quantidade).

## Dependências

- **Router**, rota pública `cart_update`.
- **CartService** (`updateCartItemQuantity`, `updateCartItemQuantityCookie`, `getCartByUserId`, `getCartItemsFromCookie`, `saveCartCookie`).
- **DB** (`dbPrepareAndExecute`), UPDATE/DELETE tipados.
- **Response** (`redirect`), saída 302.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| `decrease` remove o item quando quantidade chega a 1 (equivalente ao remove) | `src/Services/Cart/CartService.php:126-137` | 🟢 |
| Retorno do serviço usado para flash de erro em falha de banco (P10); cookie segue sem feedback | `src/Controllers/Cart/Cart.php:78-83` | 🟢 |
| UPDATE/DELETE por `id` do item (não por produto) no DB | `src/Services/Cart/CartService.php:117,130,141` | 🟢 |
| Cookie reindexado com `array_values` após remoção | `src/Services/Cart/CartService.php:286` | 🟢 |
| Whitelist de ações validada apenas no controller | `src/Controllers/Cart/Cart.php:72` | 🟢 |

## Estado Interno

- Logado: `cart_items.quantity` (UPDATE) ou linha removida (DELETE).
- Visitante: cookie `cart_items` regravado.

## Observabilidade

- Nenhum log.

## Riscos e Lacunas

- 🟢 Limite de estoque no `increase` validado (P11).
- 🟢 Falhas de banco (carrinho/item inexistente) com flash de erro (P10); falhas de cookie permanecem silenciosas (P10).
- 🟢 Sem schema pendente (migration 9).
