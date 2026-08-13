# Carrinho (GET /carrinho), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| GET | `/carrinho` | pública | — | Exibe o carrinho de compras (banco ou cookie) |

## Requisição

Sem corpo. Estado lido de `$_SESSION['user']` (logado) ou cookie `cart_items`.

**Cookie `cart_items` (visitante):** `product_id:quantity[,product_id:quantity]`, ex.: `3:2,7:1`. Pares inválidos (não inteiros ou `< 1`) são ignorados.

## Resposta

### 200 OK

- **Corpo:** HTML com:
  - Vazio → alerta "Seu carrinho está vazio." + botão "Ver produtos" (`/produtos`).
  - Com itens → tabela: Produto (imagem, nome linkado a `/produtos/{id}`, `description_line`), Preço, Quantidade (− / número / +), Subtotal, botão remover; box de Total à direita.
- Preços formatados como `R$ {preço em centavos / 100}` com `number_format(2, ',', '.')`.

**Formulários de ação (POST):**

| Form | Action | Campos |
|------|--------|--------|
| Diminuir | `/carrinho/atualizar` | `product_id`, `action=decrease` |
| Aumentar | `/carrinho/atualizar` | `product_id`, `action=increase` |
| Remover | `/carrinho/remover` | `product_id` |

## Códigos de status

| Código | Caso |
|--------|------|
| 200 | Sempre (página pública; itens vazios exibem estado vazio) |

## Exemplos

```
GET /carrinho  (logado)
→ 200  <table> ... R$ 49,90 ... Total R$ 99,80

GET /carrinho  (visitante, Cookie: cart_items=3:2,7:1)
→ 200  itens enriquecidos dos produtos 3 e 7 (apenas ativos)

GET /carrinho  (sem itens)
→ 200  "Seu carrinho está vazio." + botão Ver produtos
```

## Notas

- Não há JSON. Página pública sempre responde 200.
- Total calculado sobre os itens **exibidos**; para visitante, produtos inativos são omitidos do total.
- O carrinho em banco só existe após o primeiro "adicionar" (`addToCart` cria `carts` via `getOrCreateCart`).
- O cookie `cart_items` é texto puro e editável pelo cliente — os valores exibidos refletem o cookie (sem validação de estoque).
