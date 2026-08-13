# Carrinho Adicionar (POST /carrinho/adicionar), Requisitos

## Visão Geral

Processa a adição de um produto ao carrinho. Logado → persiste em banco (cria o carrinho se necessário); visitante → atualiza o cookie `cart_items`. Redireciona para `/carrinho` em qualquer desfecho válido e para `/produtos` quando o `product_id` é inválido.

## Responsabilidades

- Validar `product_id` (inteiro ≥ 1 via `FILTER_VALIDATE_INT`).
- Logado: `getOrCreateCart` + incremento (item existente) ou insert (item novo).
- Visitante: incremento (item existente) ou inserção no cookie `cart_items`.
- Redirecionar 302 para `/carrinho` após adição válida; 302 `/produtos` em `product_id` inválido.

## Regras de Negócio

- `product_id` inválido/ausente → redirect `/produtos` 🟢
- Logado (`isset($_SESSION['user'])`): `addToCart($connection, $userId, $productId)` 🟢
- `getOrCreateCart`: retorna carrinho existente ou cria (`INSERT INTO carts (user_id) VALUES (?)`) 🟢
- Item já no carrinho → `UPDATE cart_items SET quantity = quantity + 1` (incremento) 🟢
- Item novo → `INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, 1)` 🟢
- Visitante: `addToCartCookie($productId)` — incrementa se existe, senão adiciona `['product_id' => $id, 'quantity' => 1]` 🟢
- Cookie regravado com `setcookie('cart_items', ..., time() + 86400 * 30, '/')` 🟢
- Cookie `cart_items` é apenas **indicador de quantidade** de itens selecionados (P8) — formato simples sem assinatura/integridade 🟢
- **Validação de estoque na adição (P11):** a quantidade não pode exceder `stock` do produto 🟢
- **Validação de existência + `active` do produto (P12):** produto inexistente ou inativo não é adicionado ao carrinho 🟢
- **Cookie silencioso (P10):** operações sobre `cart_items` (cookie) permanecem sem feedback — é apenas indicador de quantidade; incremento/inserção sempre responde `success` e 302 `/carrinho` 🟢
- **Feedback de falha de banco (P10):** INSERT/UPDATE do carrinho logado que falha ou afeta 0 linhas → flash de erro e 302 `/carrinho` (não fingir sucesso) 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Adicionar produto (logado) | Must | POST válido → item inserido/incrementado em `cart_items` e 302 `/carrinho` |
| RF-02 | Criar carrinho do usuário sob demanda | Must | 1ª adição logado → `INSERT carts` |
| RF-03 | Adicionar produto (visitante) | Must | POST válido → cookie `cart_items` atualizado e 302 `/carrinho` |
| RF-04 | Incrementar item existente | Must | produto repetido → `quantity + 1` (DB ou cookie) |
| RF-05 | Redirecionar em id inválido | Must | `product_id` inválido → 302 `/produtos` |
| RF-06 | Validar `product_id` | Must | `FILTER_VALIDATE_INT` com `min_range => 1` |
| RF-07 | Validar estoque e produto ativo (P11/P12) | Must | adição bloqueada quando `stock` é insuficiente ou produto inativo |
| RF-08 | Reportar falha de banco (P10) | Must | logado: INSERT/UPDATE com falha ou 0 linhas → flash de erro e 302 `/carrinho`; cookie segue silencioso |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | SQL tipado (`dbPrepareAndExecute`) | `src/Services/Cart/CartService.php:53-89` | 🟢 |
| Segurança | Entrada validada antes de qualquer escrita | `src/Controllers/Cart/Cart.php:47-54` | 🟢 |
| Correção | Quantidade mínima 1 (cookie) | `src/Services/Cart/CartService.php:266-267` | 🟢 |
| Correção | Cookie com validade de 30 dias | `src/Services/Cart/CartService.php:249` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um usuário logado
Quando envia POST "/carrinho/adicionar" com product_id válido
Então o item é inserido (ou incrementado) em cart_items e recebe 302 Location "/carrinho"

Dado um visitante
Quando envia POST "/carrinho/adicionar" com product_id válido
Então o cookie cart_items é atualizado e recebe 302 Location "/carrinho"

Quando envia POST "/carrinho/adicionar" com product_id inválido
Então recebe 302 Location "/produtos" sem alteração de carrinho

Dado um usuário logado e o banco indisponível
Quando envia POST "/carrinho/adicionar" com product_id válido
Então recebe 302 Location "/carrinho" com flash de erro (a adição não foi confirmada)
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Adicionar logado e visitante | Must | Núcleo da ação |
| Incremento de item existente | Must | Sem duplicidade |
| Redirect 302 | Must | PRG (Post/Redirect/Get) |
| Validação do id | Must | Integridade da entrada |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:179-190` | rota `cart_add` (POST `/carrinho/adicionar`, `doAddToCart`, sem middlewares) | 🟢 |
| `src/Controllers/Cart/Cart.php:45-63` | `doAddToCart` | 🟢 |
| `src/Services/Cart/CartService.php:26-51` | `createCart` / `getOrCreateCart` | 🟢 |
| `src/Services/Cart/CartService.php:53-89` | `addToCart` | 🟢 |
| `src/Services/Cart/CartService.php:252-273` | `addToCartCookie` | 🟢 |
| `src/Services/Cart/CartService.php:241-250` | `saveCartCookie` | 🟢 |
