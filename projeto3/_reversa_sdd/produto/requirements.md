# Produto (GET /produtos/{id}), Requisitos

## Visão Geral

Página pública de detalhe de um único produto do catálogo. Busca o produto por **ID** (último segmento da URI), exibe dados completos (imagem, nome, descrição curta, preço, estoque, descrição longa), breadcrumb da categoria e seção "Produtos em destaque". Produto inexistente/inativo → HTTP 404.

## Responsabilidades

- Resolver o ID do produto a partir do último segmento da URI (`/produtos/{id}`).
- Buscar o produto ativo de categoria ativa por ID.
- Retornar HTTP 404 quando o produto não existe, é inativo ou a categoria é inativa.
- Renderizar a página de detalhe com breadcrumb, preço, status de estoque e botão "Comprar".
- Exibir 6 produtos aleatórios em destaque.

## Regras de Negócio

- ID extraído do último segmento da URI e validado como inteiro ≥ 1 (`FILTER_VALIDATE_INT`, `min_range` 1); valor não numérico/inválido → `null` → 404 🟢
- Consulta exige `p.active = true` e `c.active = true` — produto inativo ou de categoria inativa retorna 404 🟢
- A consulta usa `LIMIT 1` e retorna a primeira linha (`mysqli_fetch_assoc`) 🟢
- Rota regex é permissiva (`[a-zA-Z0-9]+`), mas apenas IDs inteiros resolvem; `/produtos/abc` → 404 🟢
- Preço exibido como `R$` com centavos (preço em centavos `/ 100`, `number_format` pt-BR) 🟢
- Status de estoque: `stock > 0` → badge "Produto em estoque"; senão → "Fora de estoque" 🟢
- Breadcrumb usa `category_id`/`category_name` do produto e aponta para `/produtos?categoryId={id}` 🟢
- Botão "Comprar" faz POST `/carrinho/adicionar` com `product_id` oculto (delegado a outra unit) 🟢
- `makeProduct` calcula `$productId` **duas vezes** com a mesma lógica (primeira atribuição redundante) 🟡

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Exibir detalhe do produto ativo ao acessar `/produtos/{id}` | Must | GET `/produtos/3` retorna 200 com nome, preço, imagem e descrições |
| RF-02 | Retornar 404 para produto inexistente | Must | GET `/produtos/9999` retorna 404 com corpo "not found" |
| RF-03 | Retornar 404 para ID não numérico | Must | GET `/produtos/abc` retorna 404 |
| RF-04 | Exibir breadcrumb da categoria com link para a listagem filtrada | Must | Breadcrumb mostra `category_name` e link `/produtos?categoryId={id}` |
| RF-05 | Exibir status de estoque | Must | `stock > 0` → badge verde; `stock = 0` → badge vermelho |
| RF-06 | Permitir adicionar ao carrinho | Must | Botão "Comprar" faz POST `/carrinho/adicionar` com `product_id` |
| RF-07 | Exibir 6 produtos aleatórios em destaque | Should | Seção "Produtos em destaque" com até 6 cards |
| RF-08 | Exibir descrição longa do produto | Must | `$product['description']` renderizada em `product_description` |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Rota pública, sem middleware de autenticação | `src/Configs/routes.php:62-70` | 🟢 |
| Segurança | Views escapam dados do banco com `htmlspecialchars` (P7 — corrigido na reimplementação) | `src/Components/Product/product_header.php:14-27` | 🟢 |
| Correção | Colunas `description_line`/`short_description` são reais e usadas; a migration 7 deve rodar **sem** as cláusulas `AFTER` (P2) | `src/Migrations/7_add_product_short_description.php:11` | 🟢 |
| Performance | Consulta com `LIMIT 1` e lookup por PK (`p.id`) | `src/Services/Products/ProductsService.php:145-166` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um produto ativo de categoria ativa
Quando o visitante acessa GET "/produtos/{id}"
Então recebe HTTP 200 com a página de detalhe e preço formatado em R$

Dado um produto inexistente
Quando o visitante acessa GET "/produtos/{id}"
Então recebe HTTP 404 com corpo "not found"

Dado um ID não numérico na URI
Quando o visitante acessa GET "/produtos/abc"
Então recebe HTTP 404

Dado um produto com stock igual a zero
Quando o visitante acessa GET "/produtos/{id}"
Então o badge "Fora de estoque" é exibido
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Detalhe do produto ativo | Must | Caminho crítico da página |
| 404 para inexistente/inativo | Must | Contrato de erro sem alternativa |
| Breadcrumb e estoque | Should | Auxilia navegação e decisão de compra |
| Adicionar ao carrinho | Must | Integração com o fluxo de compra |
| Destaques aleatórios | Should | Apresentação secundária |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:62-70` | rota `product` (GET regex, `makeProduct`) | 🟢 |
| `src/Controllers/Products/Products.php:52-70` | `makeProduct` | 🟢 |
| `src/Services/Products/ProductsService.php:121-172` | `getProductById` | 🟢 |
| `src/Pages/Products/product.php` | view da página | 🟢 |
| `src/Components/Product/product_breadcrumb.php` | breadcrumb | 🟢 |
| `src/Components/Product/product_header.php` | cabeçalho/preço/estoque/comprar | 🟢 |
| `src/Components/Product/product_description.php` | descrição longa | 🟢 |
| `src/Components/RandomProducts/random_products_cards.php` | cards de destaque | 🟢 |
