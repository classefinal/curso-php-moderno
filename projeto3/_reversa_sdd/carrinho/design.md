# Carrinho (GET /carrinho), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| GET | `/carrinho` | sessão (`$_SESSION['user']`) ou cookie `cart_items` | HTML do carrinho | 200 |

Parâmetros da view: `title`, `routes`, `items` (array `CartItem[]`), `total` (int centavos).

## Fluxo Principal

### Usuário logado
1. `makeCart` detecta `isset($_SESSION['user'])`. `src/Controllers/Cart/Cart.php:16`
2. `$cart = getCartByUserId($connection, (int)$user['id'])` — `SELECT * FROM carts WHERE user_id = ? LIMIT 1`. `src/Services/Cart/CartService.php:9-24`
3. Com carrinho: `$items = getCartItems($connection, $cart['id'])` — JOIN `cart_items ci` + `products p`. `src/Services/Cart/CartService.php:176-194`
4. `$total = calculateCartTotal($items)` — Σ `price × quantity`. `src/Services/Cart/CartService.php:196-205`

### Visitante
1. `$cookieItems = getCartItemsFromCookie()` — parse `id:qtd,id:qtd` com validação `int >= 1`. `src/Services/Cart/CartService.php:209-239`
2. Se não vazio: `$items = enrichCartItemsWithProductData(...)` — por item, `SELECT id, name, price, image, stock, description_line FROM products WHERE id = ? AND active = true`; inexistente/inativo → descartado; monta `CartItem` com `id=0`, `cart_id=0`, `created_at=''`, `updated_at=''`. `src/Services/Cart/CartService.php:320-357`
3. `$total = calculateCartTotal($items)`. `src/Services/Cart/CartService.php:196-205`

### Renderização
1. View `Cart/cart` com `title='Carrinho de compras'`, `routes`, `items`, `total`. `src/Controllers/Cart/Cart.php:35-40`
2. `$configs['response'](content: $content)` → 200. `src/Controllers/Cart/Cart.php:42`

## Fluxos Alternativos

- **Sem carrinho logado:** `$cart` null → itens vazios e total 0 (sem criação de carrinho aqui; `createCart` só em "adicionar"). `src/Services/Cart/CartService.php:22-23`, `src/Controllers/Cart/Cart.php:22-25`
- **Sem cookie:** `$cookieItems` vazio → itens vazios. `src/Services/Cart/CartService.php:211-213`
- **Cookie com produtos inativos:** itens descartados no enrich; o resto do carrinho permanece. `src/Services/Cart/CartService.php:335-337`

## Estrutura de Dados

`CartItem` (banco/JOIN): `id`, `cart_id`, `product_id`, `quantity`, `created_at`, `updated_at` + `name`, `price`, `image`, `stock`, `description_line` (de `products`). Visitante: `id`/`cart_id` = 0 e datas vazias.

Esquema: `carts` (`id`, `user_id` UNIQUE, `created_at`, `updated_at`, FK `users` CASCADE) e `cart_items` (`id`, `cart_id`, `product_id`, `quantity` default 1, FK `carts`/`products` CASCADE). `src/Migrations/9_create_carts_and_cart_items_tables.php`

## Dependências

- **Router**, resolução da rota pública.
- **CartService** (`getCartByUserId`, `getCartItems`, `calculateCartTotal`, `getCartItemsFromCookie`, `enrichCartItemsWithProductData`).
- **DB** (`dbPrepareAndExecute`), consultas tipadas.
- **View** (`createView`), renderização.
- **Response** (`response`), saída 200.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Carrinho persistido em banco (logado) vs cookie (visitante), com mesmo modelo de item | `src/Controllers/Cart/Cart.php:18-33` | 🟢 |
| Sessão sem recarga: usa `$_SESSION['user']` direto (sem middleware `auth`) | `src/Controllers/Cart/Cart.php:16-20` | 🟡 |
| Preços em centavos (inteiros), formatação BRL na view | `src/Services/Cart/CartService.php:196-205`, `src/Pages/Cart/cart.php:60` | 🟢 |
| Cookie `cart_items` com formato compacto `id:qtd` | `src/Services/Cart/CartService.php:215-235` | 🟢 |
| Enriquecimento por produto filtra ativos | `src/Services/Cart/CartService.php:329` | 🟢 |

## Estado Interno

- Somente leitura nesta rota (sessão e cookie). Nenhuma escrita.

## Observabilidade

- Nenhum log.

## Riscos e Lacunas

- 🟢 Interpolações escapadas com `htmlspecialchars` na view (P7).
- 🟡 Sessão `$_SESSION['user']` usada sem validar `active`/existência — carrinho de usuário deletado/inativo seria lido igualmente.
- 🟢 Cookie `cart_items` em texto puro, aceito como mero indicador de quantidade, sem assinatura (P8).
- 🟢 Sem dependências de schema pendente (tabelas criadas na migration 9).
