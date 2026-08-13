# Logout (GET /logout), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| GET | `/logout` | pública | — | Encerra a sessão de usuário comum |

## Requisição

Sem corpo. Sem query params relevantes.

## Resposta

### 303 See Other

- **Usuário comum (`$_SESSION['user']`):** `Location: /` — chave `user` removida da sessão.
- **Admin (`$_SESSION['admin']`):** `Location: /admin/logout` — a sessão de admin **não** é removida aqui.

## Códigos de status

| Código | Caso |
|--------|------|
| 303 | Todos os desfechos (usuário, admin, ou sem sessão) |

## Exemplos

```
GET /logout  (usuário logado)
→ 303 Location: /            (user removida)

GET /logout  (admin logado)
→ 303 Location: /admin/logout

GET /logout  (sem sessão)
→ 303 Location: /
```

## Notas

- Não há HTML nem JSON — resposta é sempre redirect 303.
- Não destrói a sessão completa; remove apenas a chave `user` (nesta rota).
- Para encerrar a sessão de admin, o fluxo é via `/admin/logout` (unidade `logout-admin`).
