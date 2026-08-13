# Produto (GET /produtos/{id}), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| GET | `/produtos/{id}` | `{id}` no último segmento da URI (int ≥ 1) | HTML da página de detalhe | 200, 404 |

Parâmetros da view (`makeProduct`): `title`, `product`, `routes`, `randomProducts`.

## Fluxo Principal

1. Requisição GET casa a regex `/^\/produtos\/[a-zA-Z0-9]+$/` → rota `product` (`makeProduct`). `src/Configs/routes.php:62-70`
2. `makeProduct` chama `getProductById($connection, $uri)`. `src/Controllers/Products/Products.php:54`
3. `getProductById` extrai o último segmento da URI (`explode('/', $uri)` + `array_last`) e valida como inteiro ≥ 1. `src/Services/Products/ProductsService.php:123-139`
4. Se ID inválido (`null`) → retorna `null`. `src/Services/Products/ProductsService.php:141-143`
5. Consulta `SELECT p.*, c.name AS category_name FROM products p INNER JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.active = true AND c.active = true LIMIT 1` com param tipado `'i'`. `src/Services/Products/ProductsService.php:145-166`
6. Se `mysqli_num_rows === 0` → `null`. `src/Services/Products/ProductsService.php:168-170`
7. `makeProduct`: se `null` → `$configs['response'](404, 'not found')` (corpo texto "not found") e encerra. `src/Controllers/Products/Products.php:56-60`
8. Senão monta a view `Products/product` com `title = $product['name']`, `product`, `routes` e `randomProducts = getRandomActiveProducts(...)`. `src/Controllers/Products/Products.php:62-67`
9. A view inclui `header.php`, `product_breadcrumb`, `product_header`, `product_description`, seção "Produtos em destaque" com `random_products_cards` e `footer.php`. `src/Pages/Products/product.php`
10. `$configs['response'](content: $content)` → HTTP 200. `src/Controllers/Products/Products.php:69`

## Fluxos Alternativos

- **ID não numérico ou < 1:** filtro `FILTER_VALIDATE_INT` retorna `null` → 404 "not found". `src/Services/Products/ProductsService.php:125-143`
- **Produto inativo ou categoria inativa:** a cláusula `p.active = true AND c.active = true` faz a query retornar 0 linhas → 404. `src/Services/Products/ProductsService.php:154-157`

## Dependências

- **ProductsService** (`getProductById`), busca do produto com JOIN de categoria.
- **RandomProductsService** (`getRandomActiveProducts`), destaques.
- **View** (`createView`), renderização por `extract()` + output buffering.
- **Response** (`response`), envio do HTML (200) e do corpo 404.
- **Functions** (`getMenuItens`), menu de navegação.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Identificação do produto pelo último segmento da URI, não por slug | `src/Services/Products/ProductsService.php:123-125` | 🟢 |
| Regex permissiva `[a-zA-Z0-9]+` + validação por inteiro (não numérico → 404) | `src/Configs/routes.php:64`, `src/Services/Products/ProductsService.php:125-139` | 🟢 |
| 404 retornado pelo próprio controller com corpo "not found" | `src/Controllers/Products/Products.php:56-60` | 🟢 |
| Visibilidade (produto/categoria ativos) aplicada no SQL | `src/Services/Products/ProductsService.php:154-157` | 🟢 |
| Preço em centavos formatado na view | `src/Components/Product/product_header.php:19-21` | 🟢 |
| Código duplicado: `$productId` calculado 2x (primeira versão redundante) | `src/Services/Products/ProductsService.php:123-139` | 🟡 |

## Estado Interno

- Sem estado persistente. Tudo derivado do banco por requisição.

## Observabilidade

- Nenhum log específico. O caso 404 é silencioso (sem registro).

## Riscos e Lacunas

- 🟡 XSS potencial: `name`, `short_description`, `description` e `image` impressos com `<?= ?>` sem `htmlspecialchars`. `src/Components/Product/product_header.php:14-27`, `product_description.php:12`
- 🟡 Duplicação de cálculo de `$productId` em `getProductById`.
- 🟡 Rota aceita qualquer slug alfanumérico na regex, gerando request "válida" que termina em 404 silencioso.
