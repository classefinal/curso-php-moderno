# User Story — Carrinho

> Fluxo de usuário cobrindo as units: `carrinho/`, `carrinho-adicionar/`, `carrinho-atualizar/` e `carrinho-remover/`.

## Narrativa

Um usuário (logado ou visitante) navega o catálogo e adiciona produtos ao carrinho. Ele ajusta quantidades, remove itens e confere o total. Logados têm o carrinho persistido em banco (`carts`/`cart_items`); visitantes usam o cookie `cart_items`.

## Persona

- **Usuário logado**: carrinho persistido no banco.
- **Visitante**: carrinho em cookie (`id:qtd,id:qtd`, 30 dias).

## Jornada

1. Na página de produto/detalhe, o usuário clica "Adicionar" → `POST /carrinho/adicionar`. 🟢 `src/Controllers/Cart/Cart.php:45-63`
2. Redirecionado para `GET /carrinho` (PRG). 🟢
3. Logado: item inserido em `cart_items` (qtd 1) ou incrementado (qtd +1); carrinho criado sob demanda. 🟢 `src/Services/Cart/CartService.php:53-89`
4. Visitante: cookie `cart_items` atualizado com o par `id:qtd`. 🟢 `src/Services/Cart/CartService.php:252-273`
5. Na página do carrinho, ajusta a quantidade com os botões − / + (`POST /carrinho/atualizar` com `action`). 🟢 `src/Controllers/Cart/Cart.php:65-84`
6. `decrease` com quantidade 1 **remove** o item. 🟢 `src/Services/Cart/CartService.php:126-137`
7. Remove um item por completo com o botão lixeira (`POST /carrinho/remover`). 🟢 `src/Controllers/Cart/Cart.php:86-104`
8. Confere o total (Σ `price × quantity`, em centavos, formatado R$). 🟢 `src/Services/Cart/CartService.php:196-205`

## Regras observadas no código

| Regra | Evidência | Confiança |
|-------|-----------|-----------|
| Backend duplo: banco (logado) vs cookie (visitante) — ADR-006 | `src/Controllers/Cart/Cart.php:56-60` | 🟢 |
| Incremento em vez de duplicação de linha | `src/Services/Cart/CartService.php:66-77` | 🟢 |
| `decrease` remove o item em quantidade 1 | `src/Services/Cart/CartService.php:126-137` | 🟢 |
| Produtos inativos filtrados no enrich do visitante | `src/Services/Cart/CartService.php:329` | 🟢 |
| Total sempre em centavos (inteiros) | `src/Services/Cart/CartService.php:196-205` | 🟢 |

## Critérios de Aceite

```gherkin
Dado um usuário logado
Quando adiciona um produto ao carrinho
Então o item aparece em /carrinho com o total atualizado

Dado um visitante com cookie
Quando adiciona um produto ao carrinho
Então o item aparece em /carrinho (enriquecido do banco, apenas ativos)

Dado um carrinho com item de quantidade 1
Quando o usuário clica em diminuir
Então o item é removido do carrinho

Dado um carrinho com itens
Quando o usuário clica em remover um item
Então o item sai por completo, independente da quantidade
```

## Métricas de sucesso (sugeridas)

- Abandono de carrinho (sem fluxo de checkout no legado).
- Ticket médio (soma de totais).
- Taxa de itens removidos vs. finalizados.

## Pontos de atenção

- 🟡 Sem limite de estoque na adição/atualização.
- 🟡 Cookie de visitante é texto puro, editável pelo cliente (sem assinatura).
- 🟢 Sem tela de checkout/compra no legado — o carrinho termina na página de total.
