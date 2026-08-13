# Carrinho Atualizar (POST /carrinho/atualizar), Requisitos

## Visão Geral

Processa o aumento ou a diminuição da quantidade de um item do carrinho. Ação `increase` soma 1; `decrease` subtrai 1 e, ao chegar em quantidade 1, **remove** o item. Redireciona 302 para `/carrinho` em qualquer desfecho válido.

## Responsabilidades

- Validar `product_id` (inteiro ≥ 1) e `action` (whitelist `increase`/`decrease`).
- Logado: `updateCartItemQuantity` — UPDATE de quantidade ou DELETE quando quantidade 1 em `decrease`.
- Visitante: `updateCartItemQuantityCookie` — mesmo comportamento sobre o cookie.
- Redirecionar 302 `/carrinho`; em entrada inválida, redirecionar 302 `/carrinho` sem alteração.

## Regras de Negócio

- `product_id` inválido ou `action` fora da whitelist → redirect `/carrinho` (sem erro) 🟢
- Logado: `updateCartItemQuantity($connection, $userId, $productId, $action)` 🟢
- Carrinho inexistente → retorna `['success' => false, 'error' => 'Carrinho não encontrado']` (controller ignora) 🟢
- Item inexistente → retorna `['success' => false, 'error' => 'Item não encontrado']` (controller ignora) 🟢
- `increase` → `UPDATE cart_items SET quantity = quantity + 1 WHERE id = ?` 🟢
- `decrease` com `quantity <= 1` → `DELETE FROM cart_items WHERE id = ?` (remove o item) 🟢
- `decrease` com `quantity > 1` → `UPDATE cart_items SET quantity = quantity - 1 WHERE id = ?` 🟢
- `action` inválido no serviço → `['success' => false, 'error' => 'Ação inválida']` 🟢
- Visitante: mesmo comportamento sobre `cart_items` (cookie) 🟢
- `increase` **respeita `stock`** (P11): a quantidade não pode exceder o estoque do produto 🟢
- **Cookie silencioso (P10):** operações sobre `cart_items` (cookie) permanecem sem feedback — item ausente não gera erro, apenas 302 `/carrinho` 🟢
- **Feedback de falha de banco (P10):** UPDATE/DELETE do carrinho logado com falha ou 0 linhas → flash de erro e 302 `/carrinho` (não fingir sucesso) 🟢
- Controller (P10): em falha de banco redireciona 302 `/carrinho` com flash de erro; cookie segue o comportamento atual 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Aumentar quantidade | Must | POST `action=increase` → `quantity + 1` (DB/cookie) e 302 `/carrinho` |
| RF-02 | Diminuir quantidade | Must | POST `action=decrease` → `quantity - 1` e 302 `/carrinho` |
| RF-03 | Remover item em quantidade 1 | Must | `decrease` com qtd ≤ 1 → item removido |
| RF-04 | Rejeitar ação desconhecida | Must | `action` fora de whitelist → 302 `/carrinho` sem alteração |
| RF-05 | Validar `product_id` | Must | inteiro ≥ 1 via `FILTER_VALIDATE_INT` |
| RF-06 | Suportar logado e visitante | Must | DB para sessão, cookie para visitante |
| RF-07 | Reportar falha de banco (P10) | Must | logado: UPDATE/DELETE com falha ou 0 linhas → flash de erro e 302 `/carrinho`; cookie segue silencioso |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | SQL tipado em todas as escritas | `src/Services/Cart/CartService.php:91-151` | 🟢 |
| Segurança | Whitelist de ações no controller | `src/Controllers/Cart/Cart.php:72` | 🟢 |
| Correção | `array_values` após `unset` (cookie) preserva índice | `src/Services/Cart/CartService.php:286` | 🟢 |
| Correção | DELETE atômico por `id` do item | `src/Services/Cart/CartService.php:128-137` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um carrinho com item (quantidade > 1)
Quando envia POST "/carrinho/atualizar" com action=decrease
Então a quantidade é reduzida em 1 e recebe 302 Location "/carrinho"

Dado um carrinho com item de quantidade 1
Quando envia POST "/carrinho/atualizar" com action=decrease
Então o item é removido e recebe 302 Location "/carrinho"

Dado um carrinho com item
Quando envia POST "/carrinho/atualizar" com action=increase
Então a quantidade é aumentada em 1 e recebe 302 Location "/carrinho"

Quando envia POST "/carrinho/atualizar" com action inválida
Então recebe 302 Location "/carrinho" sem alteração

Dado um usuário logado e o UPDATE sem efeito (item não existe no carrinho)
Quando envia POST "/carrinho/atualizar" com action=increase
Então recebe 302 Location "/carrinho" com flash de erro
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Aumentar/diminuir quantidade | Must | Núcleo da ação |
| Remover item em qtd 1 | Must | Comportamento de `decrease` |
| Whitelist de ações | Must | Controle de entrada |
| PRG 302 | Must | Padrão de formulário |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:191-202` | rota `cart_update` (POST `/carrinho/atualizar`, `doUpdateCartQuantity`, sem middlewares) | 🟢 |
| `src/Controllers/Cart/Cart.php:65-84` | `doUpdateCartQuantity` | 🟢 |
| `src/Services/Cart/CartService.php:91-151` | `updateCartItemQuantity` | 🟢 |
| `src/Services/Cart/CartService.php:275-298` | `updateCartItemQuantityCookie` | 🟢 |
