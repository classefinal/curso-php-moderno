# Carrinho (GET /carrinho), Requisitos

## Visão Geral

Página pública do carrinho de compras. Para usuário logado, lê itens persistidos nas tabelas `carts`/`cart_items`; para visitante, lê o carrinho do cookie `cart_items`. Exibe a lista de itens (produto, preço, quantidade, subtotal), controles de quantidade/remoção e o total.

## Responsabilidades

- Carregar itens do carrinho conforme o estado de autenticação.
- Logado → carrinho em banco (`carts` por `user_id`, `cart_items` com JOIN em `products`).
- Visitante → carrinho do cookie `cart_items` enriquecido com dados do produto.
- Calcular o total (preço em centavos × quantidade).
- Renderizar lista com ações POST (`/carrinho/atualizar`, `/carrinho/remover`) e link para `/produtos`.

## Regras de Negócio

- Rota pública, **sem middlewares** 🟢
- Autenticação checada apenas por `isset($_SESSION['user'])` (não valida `active` nem recarrega do banco) 🟢
- Logado: `getCartByUserId` (`WHERE user_id = ? LIMIT 1`); sem carrinho → itens vazios (carrinho é criado no primeiro "adicionar") 🟢
- Logado: itens via `getCartItems` — `SELECT ci.*, p.name, p.price, p.image, p.stock, p.description_line ... INNER JOIN products p ON ci.product_id = p.id WHERE ci.cart_id = ?` 🟢
- **Produtos inativos não são exibidos no carrinho** — nem para logado, nem para visitante (P12) 🟢
- Visitante: cookie `cart_items` no formato `id:qtd,id:qtd`; pares inválidos descartados 🟢
- Visitante: `enrichCartItemsWithProductData` ignora produtos inativos/inexistentes (`WHERE id = ? AND active = true`) 🟢
- Total = Σ `price × quantity` (inteiros em centavos) 🟢
- Preços exibidos como `R$ X.XXX,XX` via `number_format($value / 100, 2, ',', '.')` 🟢
- Imagens e nomes renderizados com `htmlspecialchars` (P7 — corrigido na reimplementação) 🟢
- Cookie `cart_items` é apenas **indicador de quantidade** de itens selecionados (P8) — sem assinatura

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Exibir carrinho logado | Must | GET `/carrinho` logado → 200 com itens e total |
| RF-02 | Exibir carrinho de visitante (cookie) | Must | GET `/carrinho` com `cart_items` → 200 com itens e total |
| RF-03 | Exibir carrinho vazio | Must | sem itens → alerta "Seu carrinho está vazio." + botão "Ver produtos" |
| RF-04 | Calcular total | Must | Σ `price × quantity` em centavos |
| RF-05 | Oferecer controles de quantidade | Must | forms POST `/carrinho/atualizar` (`increase`/`decrease`) |
| RF-06 | Oferecer remoção de item | Must | form POST `/carrinho/remover` por item |
| RF-07 | Exibir preço formatado BRL | Must | `number_format($v / 100, 2, ',', '.')` com prefixo `R$` |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Consultas tipadas (`dbPrepareAndExecute`) sem SQL injection | `src/Services/Cart/CartService.php:9-24,176-194` | 🟢 |
| Confiabilidade | Cookie `cart_items` validado campo a campo (int ≥ 1) | `src/Services/Cart/CartService.php:209-239` | 🟢 |
| Segurança | Produtos inativos não aparecem para visitante | `src/Services/Cart/CartService.php:329` | 🟢 |
| Correção | Total em centavos (int), evitando float | `src/Services/Cart/CartService.php:196-205` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um usuário logado com carrinho em banco
Quando acessa GET "/carrinho"
Então recebe 200 com a lista de itens (JOIN products) e o total correto

Dado um visitante com cookie cart_items
Quando acessa GET "/carrinho"
Então recebe 200 com itens enriquecidos (produtos ativos apenas) e o total correto

Dado um carrinho sem itens
Quando acessa GET "/carrinho"
Então recebe 200 com "Seu carrinho está vazio." e botão para /produtos
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Exibir carrinho logado e de visitante | Must | Núcleo da página |
| Total e formato BRL | Must | Informação central |
| Controles de quantidade/remoção | Must | Ações essenciais (formulários) |
| Estado vazio | Should | UX mínima |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:167-178` | rota `cart_page` (GET `/carrinho`, `makeCart`, sem middlewares) | 🟢 |
| `src/Controllers/Cart/Cart.php:12-43` | `makeCart` | 🟢 |
| `src/Services/Cart/CartService.php:9-24` | `getCartByUserId` | 🟢 |
| `src/Services/Cart/CartService.php:176-194` | `getCartItems` | 🟢 |
| `src/Services/Cart/CartService.php:196-205` | `calculateCartTotal` | 🟢 |
| `src/Services/Cart/CartService.php:209-239` | `getCartItemsFromCookie` | 🟢 |
| `src/Services/Cart/CartService.php:320-357` | `enrichCartItemsWithProductData` | 🟢 |
| `src/Pages/Cart/cart.php` | view do carrinho | 🟢 |
