# Produtos (GET /produtos), Requisitos

## Visão Geral

Página pública de listagem do catálogo de produtos. Exibe produtos **ativos** de categorias **ativas**, com paginação, filtro por categoria e seletor de quantidade de itens por página. Inclui seção "Produtos em destaque" com 6 produtos aleatórios.

## Responsabilidades

- Listar produtos ativos de categorias ativas, paginado.
- Filtrar por categoria ativa (`?categoryId=`).
- Controlar a quantidade de itens por página (`?limit=`, 5–30, default 10).
- Exibir categoria ativa selecionada (título e descrição) quando um filtro de categoria é aplicado.
- Exibir 6 produtos aleatórios em destaque (`ORDER BY RAND() LIMIT 6`).
- Permitir adicionar produto ao carrinho via formulário POST `/carrinho/adicionar` (delegado a outra unit).

## Regras de Negócio

- Só produtos com `active = true` e categoria com `active = true` aparecem na listagem (`WHERE p.active = true AND c.active = true`) 🟢
- `categoryId`: inteiro ≥ 1 via `FILTER_VALIDATE_INT`; valor inválido/ausente → `null` (lista sem filtro) 🟢
- `limit`: inteiro entre 5 e 30; fora do intervalo → default 10 🟢
- `page`: inteiro ≥ 1; default 1; OFFSET = `(page - 1) * limit` 🟢
- Categoria ativa selecionada é carregada por `getActiveCategoryById()` **sem** filtrar `active` — uma categoria inativa passada explicitamente ainda aparece como "categoria ativa" 🟢
- Título da página: `Produtos` ou `Produtos - {nome da categoria}` quando filtrada 🟢
- Se a categoria selecionada tem `description` não vazia, ela é exibida; senão, o fallback textual "Compre hoje mesmo com descontos incríveis." 🟢
- Sem produtos para os filtros → componente de estado vazio com link "Ir para produtos" 🟢
- Preços exibidos como `R$` com centavos (preço em centavos dividido por 100, formatado pt-BR) 🟢
- Rota `products` marcada `inMenu=true`, `order=1` e `allowedRoutes=['product']` — fica ativa no menu também na página de detalhe 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Listar produtos ativos de categorias ativas ao acessar `/produtos` | Must | GET `/produtos` retorna 200 com os produtos ativos |
| RF-02 | Paginar com `?page=` e `?limit=` | Must | `?page=2&limit=10` retorna o OFFSET correto |
| RF-03 | Filtrar por categoria com `?categoryId=` | Must | `?categoryId=2` lista apenas produtos da categoria 2 |
| RF-04 | Exibir seção "Produtos em destaque" com 6 aleatórios | Must | 6 cards renderizados, oriundos de `ORDER BY RAND() LIMIT 6` |
| RF-05 | Exibir estado vazio quando não há produtos | Should | Sem produtos → componente `Empty` com link para `/produtos` |
| RF-06 | Adicionar ao carrinho a partir do card | Must | Botão "Comprar" faz POST `/carrinho/adicionar` com `product_id` |
| RF-07 | Navegar para o detalhe do produto | Must | Link "Detalhes" e imagem/título apontam para `/produtos/{id}` |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Performance | Paginação via `LIMIT ? OFFSET ?` no SQL, sem carregar a lista completa | `src/Services/Products/ProductsService.php:51` | 🟢 |
| Segurança | Rota pública, sem middleware de autenticação | `src/Configs/routes.php:62-70` | 🟢 |
| Segurança | Views escapam dados do banco com `htmlspecialchars` (P7 — corrigido na reimplementação) | `src/Components/Products/product_card.php:13-23` | 🟢 |
| Correção | Colunas `description_line`/`short_description` são reais e usadas; a migration 7 deve rodar **sem** as cláusulas `AFTER` (P2) | `src/Migrations/7_add_product_short_description.php:11` | 🟢 |
| Escalabilidade | Seção de destaque usa `ORDER BY RAND()` (O(n) no banco) | `src/Services/Products/RandomProductsService.php:18` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado produtos e categorias ativos no banco
Quando um visitante acessa GET "/produtos"
Então recebe HTTP 200 com a listagem paginada e 6 produtos em destaque

Dado uma categoria válida ativa
Quando o visitante acessa GET "/produtos?categoryId={id}"
Então apenas produtos daquela categoria são listados e o título é "Produtos - {nome}"

Dado um filtro sem resultados
Quando o visitante acessa GET "/produtos?categoryId={id}"
Então o componente de estado vazio é exibido com link para "/produtos"

Dado um valor inválido de limit ("limit=99")
Quando o visitante acessa GET "/produtos?limit=99"
Então o filtro retorna o default de 10 itens por página
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Listagem de produtos ativos | Must | Caminho crítico do catálogo |
| Paginação e filtro por categoria | Must | Navegação central da página |
| Adicionar ao carrinho | Must | Vínculo com o fluxo de compra |
| Destaques aleatórios | Should | Apresentação secundária da página |
| Estado vazio | Should | Qualidade de UX em filtros sem resultado |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:50-61` | rota `products` (GET `/produtos`, `makeProducts`) | 🟢 |
| `src/Controllers/Products/Products.php:20-44` | `makeProducts` | 🟢 |
| `src/Services/Products/ProductsService.php` | `getActiveProducts`, `getActiveProductsParams`, `getActiveProductsQuery` | 🟢 |
| `src/Services/Products/RandomProductsService.php` | `getRandomActiveProducts` | 🟢 |
| `src/Services/Categories/CategoriesService.php` | `getActiveCategories`, `getActiveCategoryById` | 🟢 |
| `src/Pages/Products/products.php` | view da página | 🟢 |
| `src/Components/Products/products_list.php` | lista/cards de produtos | 🟢 |
| `src/Components/Products/product_card.php` | card de produto | 🟢 |
| `src/Components/Products/aside_menu.php` | filtros (limite/categorias) | 🟢 |
| `src/Components/Products/categories_accordion_list.php` | lista de categorias | 🟢 |
| `src/Components/RandomProducts/random_products_cards.php` | cards de destaque | 🟢 |
