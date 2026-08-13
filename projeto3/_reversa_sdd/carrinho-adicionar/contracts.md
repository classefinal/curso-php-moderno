# Carrinho Adicionar (POST /carrinho/adicionar), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| POST | `/carrinho/adicionar` | pública | `application/x-www-form-urlencoded` | Adiciona um produto ao carrinho (banco ou cookie) |

## Requisição

**Body (form-urlencoded):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `product_id` | int | sim | `FILTER_VALIDATE_INT` com `min_range => 1`; inválido → redirect `/produtos` |

## Resposta

### 302 Found

- **`product_id` válido:** `Location: /carrinho`.
- **Efeitos:**
  - Logado: carrinho criado se necessário (`carts`), item em `cart_items` com `quantity = 1` (novo) ou `quantity + 1` (existente).
  - Visitante: cookie `cart_items` regravado (`id:qtd[,id:qtd]`, validade 30 dias) com incremento ou novo item.
- **`product_id` inválido/ausente:** `Location: /produtos` — nenhuma escrita.

## Códigos de status

| Código | Caso |
|--------|------|
| 302 | Todos os desfechos (id válido → `/carrinho`; inválido → `/produtos`) |

## Exemplos

```
POST /carrinho/adicionar
product_id=3
→ 302 Location: /carrinho   (item 3 qtd 1; logado cria carts se preciso)

POST /carrinho/adicionar
product_id=3   (item já no carrinho)
→ 302 Location: /carrinho   (quantity 1 → 2)

POST /carrinho/adicionar
product_id=abc
→ 302 Location: /produtos
```

## Notas

- Não há JSON. Resposta é sempre redirect 302 (padrão PRG).
- **Não valida estoque:** a quantidade pode exceder `stock`.
- **Produto inexistente no banco (logado):** o INSERT em `cart_items` viola a FK `products(id)` e gera erro não tratado.
- Cookie não assinado: `id:qtd` em texto puro, editável pelo cliente.
