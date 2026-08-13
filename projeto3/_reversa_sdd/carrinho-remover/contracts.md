# Carrinho Remover (POST /carrinho/remover), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| POST | `/carrinho/remover` | pública | `application/x-www-form-urlencoded` | Remove um produto do carrinho por completo |

## Requisição

**Body (form-urlencoded):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `product_id` | int | sim | `FILTER_VALIDATE_INT` com `min_range => 1`; inválido → redirect `/carrinho` sem alteração |

## Resposta

### 302 Found

- **Location:** `/carrinho` (todos os desfechos).
- **Efeitos (logado, DB):** `DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?` — item removido por inteiro (quantidade irrelevante).
- **Efeitos (visitante, cookie):** par `id:qtd` removido do cookie `cart_items`, regravado com validade 30 dias.

## Códigos de status

| Código | Caso |
|--------|------|
| 302 | Todos os desfechos (id válido ou inválido) |

## Exemplos

```
POST /carrinho/remover
product_id=3
→ 302 Location: /carrinho   (item 3 removido, qtd irrelevante)

POST /carrinho/remover
product_id=abc
→ 302 Location: /carrinho   (sem alteração)
```

## Notas

- Não há JSON. Resposta é sempre redirect 302 (PRG).
- Falhas (carrinho/item inexistentes) são silenciosas — redirect sem mensagem.
- Distinção de `decrease` (unit `carrinho-atualizar`): aqui a remoção é total, sem decremento.
