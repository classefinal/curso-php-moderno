# Produto (GET /produtos/{id}), Tarefas de Implementação

## Pré-requisitos

- [ ] Rota regex `product` (`/^\/produtos\/[a-zA-Z0-9]+$/`, GET, `makeProduct`) registrada
- [ ] Tabelas `products`/`categories` com dados de exemplo
- [ ] `dbPrepareAndExecute` com args tipados disponível

## Tarefas

- [ ] T-01, Implementar `getProductById(mysqli, string $uri)` extraindo o último segmento da URI e validando como inteiro ≥ 1 via `FILTER_VALIDATE_INT`
  - Origem no legado: `src/Services/Products/ProductsService.php:121-143`
  - Critério de pronto: segmento não numérico retorna `null`
  - Confiança: 🟢

- [ ] T-02, Implementar a consulta do produto com `SELECT p.*, c.name AS category_name ... WHERE p.id = ? AND p.active = true AND c.active = true LIMIT 1` e param tipado `'i'`
  - Origem no legado: `src/Services/Products/ProductsService.php:145-166`
  - Critério de pronto: retorna `null` quando não há linhas
  - Confiança: 🟢

- [ ] T-03, Implementar `makeProduct` respondendo 404 "not found" quando o produto é `null`
  - Origem no legado: `src/Controllers/Products/Products.php:52-60`
  - Critério de pronto: `GET /produtos/9999` e `/produtos/abc` → 404
  - Confiança: 🟢

- [ ] T-04, Implementar `makeProduct` montando a view `Products/product` com `title`, `product`, `routes` e `randomProducts`
  - Origem no legado: `src/Controllers/Products/Products.php:62-70`
  - Critério de pronto: view renderiza com dados completos e responde 200
  - Confiança: 🟢

- [ ] T-05, Implementar a view `src/Pages/Products/product.php` com breadcrumb, header, descrição e destaques
  - Origem no legado: `src/Pages/Products/product.php`
  - Critério de pronto: HTML com as 3 seções + destaques
  - Confiança: 🟢

- [ ] T-06, Implementar `product_breadcrumb` com link `/produtos?categoryId={category_id}` e `category_name` em destaque
  - Origem no legado: `src/Components/Product/product_breadcrumb.php`
  - Critério de pronto: breadcrumb mostra a categoria e navega para a listagem filtrada
  - Confiança: 🟢

- [ ] T-07, Implementar `product_header` com imagem, nome, `short_description`, preço formatado (`number_format($price/100, 2, ',', '.')`), badge de estoque e formulário POST `/carrinho/adicionar` com `product_id`
  - Origem no legado: `src/Components/Product/product_header.php`
  - Critério de pronto: preço em R$, badge correto (stock > 0) e botão "Comprar"
  - Confiança: 🟢

- [ ] T-08, Implementar `product_description` renderizando a descrição longa
  - Origem no legado: `src/Components/Product/product_description.php`
  - Critério de pronto: `description` presente no HTML
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Happy path: `GET /produtos/{id}` válido retorna 200 com dados do produto
- [ ] TT-02, `GET /produtos/9999` retorna 404 "not found"
- [ ] TT-03, `GET /produtos/abc` retorna 404
- [ ] TT-04, Produto inativo ou de categoria inativa retorna 404
- [ ] TT-05, Stock zero renderiza badge "Fora de estoque"

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Nenhuma específica; garantir FK `products.category_id` íntegra (migração 5)

## Ordem Sugerida

1. T-01 → T-02 (camada de dados) → T-03 → T-04 (controller)
2. Views T-05–T-08 depois do controller
3. Testes TT-01–TT-05 ao final

## Lacunas Pendentes (🔴)

- Nenhuma pendente de validação humana nesta unit.
