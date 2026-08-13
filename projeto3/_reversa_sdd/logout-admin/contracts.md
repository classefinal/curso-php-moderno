# Logout Admin (GET /admin/logout), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| GET | `/admin/logout` | pública | — | Encerra a sessão de administrador |

## Requisição

Sem corpo. Sem query params relevantes.

## Resposta

### 303 See Other

- **Admin (`$_SESSION['admin']`):** `Location: /` — chave `admin` removida da sessão.
- **Usuário comum (`$_SESSION['user']`):** `Location: /logout` — a sessão de usuário **não** é removida aqui.

## Códigos de status

| Código | Caso |
|--------|------|
| 303 | Todos os desfechos (admin, usuário comum, ou sem sessão) |

## Exemplos

```
GET /admin/logout  (admin logado)
→ 303 Location: /            (admin removida)

GET /admin/logout  (usuário comum logado)
→ 303 Location: /logout

GET /admin/logout  (sem sessão)
→ 303 Location: /
```

## Notas

- Não há HTML nem JSON — resposta é sempre redirect 303.
- Não destrói a sessão completa; remove apenas a chave `admin` (nesta rota).
- Para encerrar a sessão de usuário comum, o fluxo é via `/logout` (unidade `logout`).
