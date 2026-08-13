# Login Admin (GET /admin/login), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| GET | `/admin/login` | pública (exige não autenticado) | — (HTML) | Página do formulário de login administrativo |

> Middleware: `preventLogged`.

## Requisição

Sem corpo. Sem query params relevantes.

## Resposta

### 200 OK — formulário renderizado

- **Condição:** nenhuma sessão ativa.
- **Corpo:** HTML com form `POST /admin/login`, título "Login administrativo", campos `email` e `password`, botão "Entrar".

### 302 Found — já autenticado

- **Condição:** `$_SESSION['user']` presente → `Location: /usuario/perfil`.
- **Condição:** `$_SESSION['admin']` presente → `Location: /admin/dashboard`.

## Códigos de status

| Código | Caso |
|--------|------|
| 200 | Sem sessão — formulário exibido |
| 302 | Sessão de usuário ou admin ativa |

## Exemplos

```
GET /admin/login  (sem sessão)
→ 200 (HTML) — form POST /admin/login

GET /admin/login  (admin logado)
→ 302 Location: /admin/dashboard

GET /admin/login  (usuário logado)
→ 302 Location: /usuario/perfil
```

## Notas

- Reutiliza a mesma view e formulário do login comum, apenas com `title` e `action` diferentes.
- Não há JSON; a rota não grava sessão (apenas POST `/admin/login` o faz — unidade `autenticar-admin`).
- O redirect para `/admin/dashboard` aponta para rota inexistente (ADR-010).
