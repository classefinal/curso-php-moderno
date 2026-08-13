# Sobre (GET /sobre), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| GET | `/sobre` | pública | `text/html; charset=utf-8` | Página Sobre com formulário de contato |

## Requisição

- **Query params:** ignorados.
- **Body:** nenhum.
- **Dependência de estado:** lê `$_SESSION['flash']` (se existir).

## Resposta

### 200 OK

- **Body:** HTML da página (`src/Pages/about.php`) com formulário, mensagens flash (se houver) e iframe.
- **Headers:** `Connection: close`, `Content-length: <len>`.
- **Efeito colateral:** `$_SESSION['flash']` removido após a leitura.

## Códigos de status

| Código | Caso |
|--------|------|
| 200 | Sempre (página estática) |

## Notas

- O envio do formulário usa **POST `/sobre`** (unit `enviar-contato`), que responde com redirect 302 de volta para esta página.
