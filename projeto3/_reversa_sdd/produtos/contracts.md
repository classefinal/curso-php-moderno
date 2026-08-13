# Produtos (GET /produtos), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| GET | `/produtos` | pública | — (HTML) | Listagem paginada de produtos ativos |

## Requisição

**Query params (todos opcionais):**

| Campo | Tipo | Default | Regras |
|-------|------|---------|--------|
| `categoryId` | int | `null` (sem filtro) | ≥ 1 via `FILTER_VALIDATE_INT`; inválido → sem filtro |
| `limit` | int | `10` | entre 5 e 30; fora do intervalo → default 10 |
| `page` | int | `1` | ≥ 1; OFFSET = `(page - 1) * limit` |

## Resposta

### 200 OK (sempre)

- **Corpo:** HTML da página (`Content-Type: text/html`).
- **Seções:** título, filtros (quantidade + categorias), grid de produtos, estado vazio se aplicável, "Produtos em destaque" (6 cards).

## Códigos de status

| Código | Caso |
|--------|------|
| 200 | Todos os casos (a listagem nunca retorna erro de aplicação por query inválida) |

> Obs.: a rota irmã `GET /produtos/{id}` (unidade `produto`) retorna 404 quando o produto não existe.

## Exemplos

```
GET /produtos?categoryId=2&limit=20&page=2
→ 200 (HTML) — produtos ativos da categoria 2, OFFSET 20

GET /produtos?limit=99
→ 200 (HTML) — limit volta ao default 10

GET /produtos?categoryId=999
→ 200 (HTML) — produtos vazios (estado vazio) ou listagem sem filtro de categoria efetivo
```

## Notas

- Não há JSON: resposta é sempre HTML renderizado.
- Nenhum cookie ou estado de sessão é alterado nesta rota.
- A adição ao carrinho a partir dos cards dispara POST `/carrinho/adicionar` (contrato da unidade `carrinho-adicionar`).
