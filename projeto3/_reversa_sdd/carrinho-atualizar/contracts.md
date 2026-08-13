# Carrinho Atualizar (POST /carrinho/atualizar), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| POST | `/carrinho/atualizar` | pública | `application/x-www-form-urlencoded` | Aumenta/diminui a quantidade de um item do carrinho |

## Requisição

**Body (form-urlencoded):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `product_id` | int | sim | `FILTER_VALIDATE_INT` com `min_range => 1` |
| `action` | string | sim | whitelist: `increase` ou `decrease` |

Qualquer valor inválido → redirect `/carrinho` sem alteração.

## Resposta

### 302 Found

- **Location:** `/carrinho` (todos os desfechos).
- **Efeitos (logado, DB):**
  - `increase` → `quantity = quantity + 1`.
  - `decrease` com `quantity > 1` → `quantity = quantity - 1`.
  - `decrease` com `quantity <= 1` → item **removido** de `cart_items`.
- **Efeitos (visitante, cookie):** mesmos cálculos sobre `cart_items` no cookie `id:qtd`, regravado com validade 30 dias.

## Códigos de status

| Código | Caso |
|--------|------|
| 302 | Todos os desfechos (ação válida ou entrada inválida) |

## Exemplos

```
POST /carrinho/atualizar
product_id=3&action=increase
→ 302 Location: /carrinho   (qtd 2 → 3)

POST /carrinho/atualizar
product_id=3&action=decrease   (qtd atual 1)
→ 302 Location: /carrinho   (item 3 removido)

POST /carrinho/atualizar
product_id=3&action=tudo
→ 302 Location: /carrinho   (sem alteração)
```

## Notas

- Não há JSON. Resposta é sempre redirect 302 (PRG).
- Erros de carrinho/item inexistentes são **silenciosos** (redirect sem mensagem).
- Sem limite de estoque no `increase`.
