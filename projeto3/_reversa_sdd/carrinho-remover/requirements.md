# Carrinho Remover (POST /carrinho/remover), Requisitos

## Visão Geral

Remove um produto do carrinho inteiramente (independente da quantidade). Logado → `DELETE` em `cart_items`; visitante → remove o par do cookie `cart_items`. Redireciona 302 `/carrinho`; com `product_id` inválido, redireciona sem alteração.

## Responsabilidades

- Validar `product_id` (inteiro ≥ 1 via `FILTER_VALIDATE_INT`).
- Logado: `removeCartItem` — `DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?`.
- Visitante: `removeCartItemCookie` — `unset` do par no cookie + `array_values`.
- Redirecionar 302 `/carrinho` (válido ou inválido).

## Regras de Negócio

- `product_id` inválido/ausente → redirect `/carrinho` sem alteração 🟢
- Logado: `removeCartItem($connection, $userId, $productId)` 🟢
- Carrinho inexistente → `['success' => false, 'error' => 'Carrinho não encontrado']` (controller ignora) 🟢
- `DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?` — remove o item por completo 🟢
- **Feedback de falha de banco (P10):** DELETE com falha ou 0 linhas → flash de erro e 302 `/carrinho` (não fingir sucesso) 🟢
- Visitante: `removeCartItemCookie` — `unset` + `array_values` + `saveCartCookie` 🟢
- **Cookie silencioso (P10):** cookie sem o produto → regrava o cookie inalterado, sem feedback 🟢
- Remoção é total — **não** é um decremento 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Remover item (logado) | Must | POST válido → linha removida de `cart_items` e 302 `/carrinho` |
| RF-02 | Remover item (visitante) | Must | POST válido → par removido do cookie e 302 `/carrinho` |
| RF-03 | Remoção total | Must | quantidade irrelevante — item sai por inteiro |
| RF-04 | Redirecionar em id inválido | Must | `product_id` inválido → 302 `/carrinho` sem alteração |
| RF-05 | Reportar falha de banco (P10) | Must | logado: DELETE com falha ou 0 linhas → flash de erro e 302 `/carrinho`; cookie segue silencioso |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | DELETE tipado (`dbPrepareAndExecute`) | `src/Services/Cart/CartService.php:161-168` | 🟢 |
| Correção | `array_values` mantém cookie válido pós-remoção | `src/Services/Cart/CartService.php:307` | 🟢 |
| Correção | Remoção escopada por `cart_id` (multi-usuário) | `src/Services/Cart/CartService.php:163` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um carrinho com um item
Quando envia POST "/carrinho/remover" com product_id válido
Então o item é removido por completo e recebe 302 Location "/carrinho"

Dado um visitante com cookie cart_items
Quando envia POST "/carrinho/remover" com product_id válido
Então o par é removido do cookie e recebe 302 Location "/carrinho"

Quando envia POST "/carrinho/remover" com product_id inválido
Então recebe 302 Location "/carrinho" sem alteração

Dado um usuário logado e o DELETE sem efeito (item não existe no carrinho)
Quando envia POST "/carrinho/remover" com product_id válido
Então recebe 302 Location "/carrinho" com flash de erro
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Remover item | Must | Núcleo da ação |
| Remoção total | Must | Semântica de "remover" |
| PRG 302 | Must | Padrão de formulário |
| Validação do id | Must | Integridade da entrada |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:203-214` | rota `cart_remove` (POST `/carrinho/remover`, `doRemoveCartItem`, sem middlewares) | 🟢 |
| `src/Controllers/Cart/Cart.php:86-104` | `doRemoveCartItem` | 🟢 |
| `src/Services/Cart/CartService.php:153-171` | `removeCartItem` | 🟢 |
| `src/Services/Cart/CartService.php:300-315` | `removeCartItemCookie` | 🟢 |
