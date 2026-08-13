# Produtos (GET /produtos), Tarefas de Implementação

## Pré-requisitos

- [ ] Tabelas `products` e `categories` criadas (migrations 4 e 5) com `active`, `stock`, `price` (centavos), `image`, `category_id`, `description_line`
- [ ] DB com `dbPrepareAndExecute` (args `['type' => 'i', 'value' => $val]`) disponível
- [ ] View e Response do framework disponíveis

## Tarefas

- [ ] T-01, Implementar `getActiveProductsQuery(?int $categoryId)` montando `SELECT p.*, c.name AS category_name ... WHERE p.active = true AND c.active = true` com `AND c.id = ?` opcional
  - Origem no legado: `src/Services/Products/ProductsService.php:9-22`
  - Critério de pronto: query correta com e sem `categoryId`
  - Confiança: 🟢

- [ ] T-02, Implementar `getActiveProductsParams()` validando `categoryId` (int ≥ 1), `limit` (5–30, default 10) e `page` (≥ 1, default 1) via `FILTER_VALIDATE_INT`, montando params tipados para `LIMIT ? OFFSET ?`
  - Origem no legado: `src/Services/Products/ProductsService.php:27-78`
  - Critério de pronto: valores inválidos caem nos defaults; OFFSET = `(page - 1) * limit`
  - Confiança: 🟢

- [ ] T-03, Implementar `getActiveProducts(mysqli)` retornando `{limit, page, products, categoryId}` via `dbPrepareAndExecute` + `mysqli_fetch_all(ASSOC)`, com lista vazia se `mysqli_num_rows === 0`
  - Origem no legado: `src/Services/Products/ProductsService.php:84-114`
  - Critério de pronto: retorno com shape `ActiveProductsList`
  - Confiança: 🟢

- [ ] T-04, Implementar `getActiveCategories` (`WHERE active = true ORDER BY name`) e `getActiveCategoryById` (`WHERE id = ? LIMIT 1`, sem filtro de active)
  - Origem no legado: `src/Services/Categories/CategoriesService.php:11-48`
  - Critério de pronto: listagem ordenada; detalhe por ID
  - Confiança: 🟢

- [ ] T-05, Implementar `getRandomActiveProducts` com `ORDER BY RAND() LIMIT 6` selecionando `id, price, name, image`
  - Origem no legado: `src/Services/Products/RandomProductsService.php:11-25`
  - Critério de pronto: retorna no máximo 6 produtos ativos
  - Confiança: 🟢

- [ ] T-06, Implementar `makeProducts` orquestrando a coleta e chamando a view `Products/products` com `title` (com/sem nome da categoria), `routes`, `limit`, `products`, `categories`, `categoryId`, `activeCategory`, `randomProducts`; responder via `$configs['response']`
  - Origem no legado: `src/Controllers/Products/Products.php:20-44`
  - Critério de pronto: view recebe todos os dados e responde 200
  - Confiança: 🟢

- [ ] T-07, Implementar a view `src/Pages/Products/products.php` com h1, descrição da categoria ativa (ou fallback "Compre hoje mesmo com descontos incríveis."), `aside_menu`, `products_list` e seção "Produtos em destaque"
  - Origem no legado: `src/Pages/Products/products.php`
  - Critério de pronto: HTML renderiza título, filtros, cards e destaques
  - Confiança: 🟢

- [ ] T-08, Implementar `aside_menu` com seletor de quantidade (10/20/30 preservando `categoryId`) e accordion de categorias com destaque (bold) da categoria ativa
  - Origem no legado: `src/Components/Products/aside_menu.php`, `categories_accordion_list.php`
  - Critério de pronto: links mantêm o filtro atual; opção ativa em negrito
  - Confiança: 🟢

- [ ] T-09, Implementar `products_list` com estado vazio (componente `Empty`, link `/produtos`) e iteração dos `product_card`
  - Origem no legado: `src/Components/Products/products_list.php`
  - Critério de pronto: sem produtos → `Empty`; com produtos → grid de cards
  - Confiança: 🟢

- [ ] T-10, Implementar `product_card` com imagem/nome/descrição-link para `/produtos/{id}`, formulário POST `/carrinho/adicionar` com `product_id` e botão "Detalhes"
  - Origem no legado: `src/Components/Products/product_card.php`
  - Critério de pronto: card renderiza e os dois caminhos de navegação funcionam
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Happy path: GET `/produtos` retorna 200 com produtos ativos e 6 destaques
- [ ] TT-02, Paginação: `?page=2&limit=5` retorna OFFSET correto
- [ ] TT-03, Filtro: `?categoryId=N` lista só produtos da categoria N e título "Produtos - {nome}"
- [ ] TT-04, Estado vazio: categoria sem produtos renderiza componente `Empty`
- [ ] TT-05, Limite inválido (`limit=99`) usa default 10

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Garantir `products.category_id` populado e coerente com `categories.id` antes da primeira listagem (FK CASCADE em migração 5)

## Ordem Sugerida

1. T-01 → T-02 → T-03 (camada de dados), depois T-04 e T-05 (dependências de dados)
2. T-06 (controller) antes das views T-07–T-10, que consomem os dados que ele injeta
3. Testes TT-01–TT-05 após as views

## Lacunas Pendentes (🔴)

- Nenhuma pendente de validação humana nesta unit. (Riscos 🟡 documentados no `design.md`.)
