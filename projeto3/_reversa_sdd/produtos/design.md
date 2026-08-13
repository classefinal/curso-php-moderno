# Produtos (GET /produtos), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| GET | `/produtos` | `categoryId: ?int` (≥1), `limit: int` (5–30, default 10), `page: int` (≥1, default 1) | HTML da página de listagem | 200 |

Parâmetros da view (`makeProducts`): `title`, `routes`, `limit`, `products`, `categories`, `categoryId`, `activeCategory`, `randomProducts`.

## Fluxo Principal

1. Requisição GET `/produtos` resolvida pela rota `products` (`makeProducts`). `src/Configs/routes.php:50-61`
2. `getActiveProducts($connection)` destrutura `limit`, `products`, `categoryId`; internamente `getActiveProductsParams()` lê e valida os query params. `src/Services/Products/ProductsService.php:84-114`, `27-78`
3. Query montada por `getActiveProductsQuery($categoryId)`: `SELECT p.*, c.name AS category_name FROM products p INNER JOIN categories c ON p.category_id = c.id WHERE p.active = true AND c.active = true` + opcional `AND c.id = ?` + `LIMIT ? OFFSET ?`. Params tipados `'i'`. `src/Services/Products/ProductsService.php:9-22`, `51-77`
4. `getActiveCategories($connection)`: `SELECT * FROM categories WHERE active = true ORDER BY name`. `src/Services/Categories/CategoriesService.php:11-23`
5. Se `categoryId` presente, `getActiveCategoryById()` carrega a categoria (sem filtro de `active`). `src/Services/Categories/CategoriesService.php:30-48`
6. `getRandomActiveProducts($connection)`: `SELECT p.id, p.price, p.name, p.image ... WHERE p.active AND c.active ORDER BY RAND() LIMIT 6`. `src/Services/Products/RandomProductsService.php:11-25`
7. `makeProducts` chama a view `Products/products` com todos os dados. `src/Controllers/Products/Products.php:32-41`
8. A view inclui `header.php`, o h1 com `$title`, a descrição da categoria ativa (ou fallback), o `aside_menu` (filtros) e `products_list` (cards), a seção "Produtos em destaque" com `random_products_cards` e `footer.php`. `src/Pages/Products/products.php`
9. `$configs['response'](content: $content)` → HTTP 200, flush, defer. `src/Controllers/Products/Products.php:43`

## Fluxos Alternativos

- **Nenhum produto para os filtros:** `products_list` renderiza o componente `Empty` com link "Ir para produtos". `src/Components/Products/products_list.php:13-27`
- **`categoryId` inválido ou ausente:** `FILTER_VALIDATE_INT` retorna `null` → consulta sem `AND c.id = ?` e `activeCategory` fica `null`. `src/Services/Products/ProductsService.php:29-34`
- **`limit` fora de 5–30:** volta ao default 10. `src/Services/Products/ProductsService.php:36-42`
- **Categoria filtrada sem descrição:** página usa o texto fixo "Compre hoje mesmo com descontos incríveis." `src/Pages/Products/products.php:26-30`

## Dependências

- **ProductsService** (`getActiveProducts`, `getActiveProductsParams`, `getActiveProductsQuery`), consulta paginada de produtos.
- **RandomProductsService** (`getRandomActiveProducts`), destaques.
- **CategoriesService** (`getActiveCategories`, `getActiveCategoryById`), filtro de categorias.
- **DB** (`dbPrepareAndExecute`), execução com args tipados.
- **View** (`createView`), renderização por `extract()` + output buffering.
- **Response** (`response`), envio do HTML.
- **Functions** (`getMenuItens`), menu de navegação.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Filtro de visibilidade aplicado no SQL (`p.active AND c.active`) | `src/Services/Products/ProductsService.php:13-15` | 🟢 |
| Paginação com `LIMIT ? OFFSET ?` e params tipados | `src/Services/Products/ProductsService.php:51-62` | 🟢 |
| `categoryId` validado com `FILTER_VALIDATE_INT` + `min_range` | `src/Services/Products/ProductsService.php:29-34` | 🟢 |
| Destaques via `ORDER BY RAND() LIMIT 6` | `src/Services/Products/RandomProductsService.php:18-19` | 🟢 |
| Preço em centavos exibido com `number_format($price/100, 2, ',', '.')` | `src/Components/RandomProducts/random_products_cards.php:24` | 🟢 |
| `getActiveCategoryById` não filtra `active` (categoria inativa via URL aparece como ativa) | `src/Services/Categories/CategoriesService.php:32-35` | 🟢 |
| Duplicação de `$productId` em `getProductById` (unidade `produto`) | `src/Services/Products/ProductsService.php:123-139` | 🟡 |

## Estado Interno

- Sem estado persistente. Dados derivados diretamente do banco a cada requisição. Filtros transmitidos via query string apenas.

## Observabilidade

- Nenhum log ou métrica específicos desta página. Falhas de query propagam erro do `mysqli` (sem tratamento explícito). `src/Services/Products/ProductsService.php`

## Riscos e Lacunas

- 🟡 Views imprimem `$product['name']`, `$product['image']` e `$product['description_line']` com `<?= ?>` sem `htmlspecialchars` (XSS potencial). `src/Components/Products/product_card.php:13-23`
- 🟡 `ORDER BY RAND()` em tabelas grandes degrada performance.
- 🟡 Fallback de descrição usa texto fixo em pt-BR hardcoded na view.
