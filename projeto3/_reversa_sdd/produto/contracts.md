# Produto (GET /produtos/{id}), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| GET | `/produtos/{id}` | pública | — (HTML) | Detalhe de um produto ativo |

> Path match: regex `^\/produtos\/[a-zA-Z0-9]+$`.

## Requisição

**Path param:**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `id` | int | sim | último segmento da URI; ≥ 1 via `FILTER_VALIDATE_INT`; não numérico → 404 |

## Resposta

### 200 OK

- **Corpo:** HTML da página de detalhe (breadcrumb, imagem, nome, `short_description`, preço `R$`, badge de estoque, `description`, destaques).

### 404 Not Found

- **Corpo:** texto plano `not found`.

## Códigos de status

| Código | Caso |
|--------|------|
| 200 | Produto ativo de categoria ativa encontrado |
| 404 | Produto inexistente, inativo, categoria inativa, ou `id` não inteiro ≥ 1 |

## Exemplos

```
GET /produtos/3
→ 200 (HTML) — produto 3 ativo

GET /produtos/9999
→ 404 "not found"

GET /produtos/abc
→ 404 "not found" (regex casa, mas a validação de inteiro falha)

GET /produtos/2
→ 404 "not found" (produto ou categoria inativos)
```

## Notas

- Não há JSON; respostas são HTML (200) ou texto (404).
- O botão "Comprar" dispara POST `/carrinho/adicionar` com `product_id` (unidade `carrinho-adicionar`).
- O breadcrumb leva a `GET /produtos?categoryId={category_id}` (unidade `produtos`).
