# Carrinho Remover (POST /carrinho/remover), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| POST | `/carrinho/remover` | `product_id` (form-urlencoded) | redirect `Location: /carrinho` | 302 |
| POST | `/carrinho/remover` (id inválido) | `product_id` inválido | redirect `Location: /carrinho` | 302 |
| POST | `/carrinho/remover` (falha de banco, logado) | DELETE sem efeito | redirect `Location: /carrinho` com flash de erro | 302 |

## Fluxo Principal (logado)

1. `doRemoveCartItem` valida `product_id` (`FILTER_VALIDATE_INT`, `min_range => 1`); inválido → 302 `/carrinho`. `src/Controllers/Cart/Cart.php:88-95`
2. `removeCartItem($connection, $userId, $productId)`. `src/Controllers/Cart/Cart.php:98`
3. `getCartByUserId`; sem carrinho → `['success' => false, 'error' => 'Carrinho não encontrado']`. `src/Services/Cart/CartService.php:155-159`
4. `DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?`. `src/Services/Cart/CartService.php:161-168`
5. Em falha de banco (DELETE sem efeito) → flash de erro; caso contrário 302 `/carrinho` (P10). `src/Controllers/Cart/Cart.php:103`

## Fluxo Principal (visitante)

1. Validação idêntica no controller. `src/Controllers/Cart/Cart.php:88-95`
2. `removeCartItemCookie($productId)`: lê cookie, procura o produto. `src/Services/Cart/CartService.php:300-310`
3. Encontrou → `unset($items[$key])` + `array_values`. `src/Services/Cart/CartService.php:304-308`
4. `saveCartCookie($items)` (setcookie 30 dias). `src/Services/Cart/CartService.php:312`
5. `$configs['redirect']('/carrinho', 302)` — cookie silencioso (P10). `src/Controllers/Cart/Cart.php:103`

## Fluxos Alternativos

- **Carrinho/item inexistente (DB):** serviço retorna erro; controller redireciona 302 `/carrinho` **com flash de erro** (P10 — feedback para falhas de banco).
- **Item não presente no cookie:** cookie regravado inalterado, **sem feedback** (P10 — cookie é mero indicador de quantidade).
- **`product_id` inválido:** nenhuma escrita, redirect `/carrinho`.

## Dependências

- **Router**, rota pública `cart_remove`.
- **CartService** (`removeCartItem`, `removeCartItemCookie`, `getCartByUserId`, `getCartItemsFromCookie`, `saveCartCookie`).
- **DB** (`dbPrepareAndExecute`), DELETE tipado.
- **Response** (`redirect`), saída 302.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| DELETE escopado por `cart_id` + `product_id` (sem precisar do `id` do item) | `src/Services/Cart/CartService.php:161-168` | 🟢 |
| Remoção total (não decremento) | `src/Services/Cart/CartService.php:161-168` | 🟢 |
| Retorno do serviço usado para flash de erro em falha de banco (P10) | `src/Controllers/Cart/Cart.php:98-103` | 🟢 |
| Cookie reindexado com `array_values` após `unset` | `src/Services/Cart/CartService.php:307` | 🟢 |
| Falha de banco tem feedback; falha de cookie é silenciosa (P10) | `src/Controllers/Cart/Cart.php:92-103` | 🟢 |

## Estado Interno

- Logado: linha removida de `cart_items`.
- Visitante: cookie `cart_items` regravado.

## Observabilidade

- Nenhum log.

## Riscos e Lacunas

- 🟢 Falhas de banco com flash de erro; falhas de cookie silenciosas (P10).
- 🟢 Sem schema pendente (migration 9).
