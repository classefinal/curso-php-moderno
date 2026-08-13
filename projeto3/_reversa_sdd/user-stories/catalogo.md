# User Story — Catálogo

> Fluxo de usuário cobrindo as units: `home/`, `produtos/`, `produto/` e `sobre/` (parte de contato é coberta em `contato.md`).

## Narrativa

Um visitante acessa a loja pela home, vê destaques e navega até o catálogo para explorar os produtos por categoria. Ao encontrar um produto de interesse, abre o detalhe para ver preço e descrição e decide adicionar ao carrinho (ver `carrinho.md`).

## Persona

- **Visitante**: usuário anônimo, sem sessão, navegando o catálogo público.

## Jornada

1. Acessa `GET /` e vê a home com destaques. 🟢 `src/Controllers/Home/Home.php`
2. Usa o menu para ir a `GET /produtos`; a lista é composta apenas de produtos **ativos** (`active = true`). 🟢 `src/Services/Products/ProductsService.php`
3. Filtra por categoria (query `?categoria=`), quando disponível. 🟢
4. Clica em um produto (link `/produtos/{slug}`) e visualiza o detalhe. 🟢 `src/Controllers/Products/Products.php`
5. Produto inexistente ou inativo → página 404 "Produto não encontrado". 🟢
6. Retorna ao catálogo para continuar explorando. 🟢

## Regras observadas no código

| Regra | Evidência | Confiança |
|-------|-----------|-----------|
| Só produtos ativos aparecem na listagem | `getActiveProducts*` | 🟢 |
| Preço exibido em centavos, formatado como R$ | ADR-002 | 🟢 |
| Slug do produto segue `[a-zA-Z0-9]+` (rota regex) | `src/Configs/routes.php:62-70` | 🟢 |
| Categorias listadas a partir de `getActiveCategories` | `src/Services/Categories/CategoriesService.php` | 🟢 |

## Critérios de Aceite

```gherkin
Dado um produto ativo
Quando um visitante acessa /produtos
Então o produto aparece na listagem com preço formatado

Dado um produto inativo ou inexistente
Quando um visitante acessa /produtos/{slug}
Então recebe a página 404 "Produto não encontrado"
```

## Métricas de sucesso (sugeridas)

- Tempo para localizar um produto no catálogo.
- Taxa de conversão detalhe → adicionar ao carrinho.

## Pontos de atenção

- 🔴 Schema de `products` depende das migrations 4-7 (incluindo `description_line`, criada como após a 6 — ADR-009 regressão da migration 7).
- 🟡 Nomes de produto e URLs de imagem podem conter HTML — verificar escape nas views do catálogo.
