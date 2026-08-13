# Carrinho Adicionar (POST /carrinho/adicionar), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| POST | `/carrinho/adicionar` | `product_id` (form-urlencoded) | redirect `Location: /carrinho` | 302 |
| POST | `/carrinho/adicionar` (id inválido) | `product_id` inválido | redirect `Location: /produtos` | 302 |
| POST | `/carrinho/adicionar` (falha de banco, logado) | INSERT/UPDATE sem efeito | redirect `Location: /carrinho` com flash de erro | 302 |

## Fluxo Principal (logado)

1. `doAddToCart` valida `product_id` com `FILTER_VALIDATE_INT` (`min_range => 1`); inválido → 302 `/produtos`. `src/Controllers/Cart/Cart.php:47-54`
2. `isset($_SESSION['user'])` → `addToCart($connection, (int)$_SESSION['user']['id'], $productId)`. `src/Controllers/Cart/Cart.php:56-57`
3. `getOrCreateCart` → `getCartByUserId` ou `createCart` (`INSERT INTO carts (user_id) VALUES (?)`, retorna `id` via `mysqli_insert_id`). `src/Services/Cart/CartService.php:26-51`
4. `SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ? LIMIT 1`. `src/Services/Cart/CartService.php:57-64`
5. Existe → `UPDATE cart_items SET quantity = quantity + 1 WHERE cart_id = ? AND product_id = ?`; retorna `incremented`. `src/Services/Cart/CartService.php:66-77`
6. Não existe → `INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, 1)`; retorna `added`. `src/Services/Cart/CartService.php:79-88`
7. Em falha de banco (INSERT/UPDATE sem efeito) → flash de erro; caso contrário 302 `/carrinho` (P10). `src/Controllers/Cart/Cart.php:62`

## Fluxo Principal (visitante)

1. Validação de `product_id` idêntica. `src/Controllers/Cart/Cart.php:47-54`
2. `addToCartCookie($productId)`: lê cookie via `getCartItemsFromCookie`, procura o produto. `src/Services/Cart/CartService.php:252-263`
3. Existe → `quantity++` e `saveCartCookie`; retorna `incremented`. `src/Services/Cart/CartService.php:257-263`
4. Não existe → append `['product_id' => $productId, 'quantity' => 1]` e `saveCartCookie`; retorna `added`. `src/Services/Cart/CartService.php:266-268`
5. `saveCartCookie`: `setcookie('cart_items', 'id:qtd,...', time() + 86400 * 30, '/')`. `src/Services/Cart/CartService.php:241-250`
6. `$configs['redirect']('/carrinho', 302)` — cookie silencioso (P10). `src/Controllers/Cart/Cart.php:62`

## Fluxos Alternativos

- **Produto inexistente/inativo no DB (P12):** validar existência + `active = true` antes de escrever — produto inválido não é adicionado (sem erro de FK, sem flash).
- **Falha de banco (P10):** INSERT/UPDATE sem efeito → flash de erro e 302 `/carrinho` (não fingir sucesso).

## Estrutura de Dados

- `carts`: `id` (auto), `user_id` UNIQUE FK `users` CASCADE.
- `cart_items`: `id`, `cart_id` FK `carts` CASCADE, `product_id` FK `products` CASCADE, `quantity` default 1.
- Cookie `cart_items`: `"{product_id}:{quantity}"` separados por vírgula.

## Dependências

- **Router**, rota pública `cart_add`.
- **CartService** (`getOrCreateCart`, `addToCart`, `addToCartCookie`, `getCartItemsFromCookie`, `saveCartCookie`).
- **DB** (`dbPrepareAndExecute`), INSERT/UPDATE tipados.
- **Response** (`redirect`), saída 302.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Dois backends (banco/cookie) selecionados por `isset($_SESSION['user'])` | `src/Controllers/Cart/Cart.php:56-60` | 🟢 |
| Carrinho criado sob demanda (lazy) na 1ª adição | `src/Services/Cart/CartService.php:42-51` | 🟢 |
| Incremento em vez de duplicação de linha | `src/Services/Cart/CartService.php:66-77` | 🟢 |
| PRG: sempre redirect 302 após POST | `src/Controllers/Cart/Cart.php:52-62` | 🟢 |
| Validação de estoque e produto ativo no caminho de escrita (P11/P12) | `src/Services/Cart/CartService.php:53-89` | 🟢 |
| Feedback de falha de banco; cookie silencioso (P10) | `src/Controllers/Cart/Cart.php:52-62` | 🟢 |

## Estado Interno

- Logado: escreve `carts` (se novo) e `cart_items`.
- Visitante: regrava o cookie `cart_items` (30 dias).

## Observabilidade

- Nenhum log.

## Riscos e Lacunas

- 🟢 Adição respeita `stock` (P11) e produto ativo (P12).
- 🟢 INSERT/UPDATE sem efeito → flash de erro (P10); sem exceção de FK não tratada (validação prévia).
- 🟡 Cookie não assinado — aceito como indicador de quantidade (P8).
- 🟢 Sem schema pendente (tabelas na migration 9).
