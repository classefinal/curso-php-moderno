# Home (GET /), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| GET | `/` | pública | `text/html; charset=utf-8` | Página inicial |

## Requisição

- **Query params:** ignorados.
- **Body:** nenhum.

## Resposta

### 200 OK

- **Body:** HTML da página inicial (`src/Pages/home.php`) com título e menu.
- **Headers:** `Connection: close`, `Content-length: <len>`.

## Códigos de status

| Código | Caso |
|--------|------|
| 200 | Sempre (página estática) |

## Notas

- Rota default: acessar a raiz sem path também cai aqui (via `processRoutes`).
- Não há redirecionamento nesta rota.
- Sem cookies adicionais além do cookie de sessão padrão.
