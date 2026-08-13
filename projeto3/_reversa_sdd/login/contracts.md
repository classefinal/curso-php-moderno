# Login (GET /login), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| GET | `/login` | pública (exige não autenticado) | — (HTML) | Página do formulário de login |

> Middleware: `preventLogged`.

## Requisição

Sem corpo. Sem query params relevantes.

## Resposta

### 200 OK — formulário renderizado

- **Condição:** nenhuma sessão ativa.
- **Corpo:** HTML com form `POST /login`, campos `email` e `password`, botão "Entrar".

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
GET /login  (sem sessão)
→ 200 (HTML) — formulário de login

GET /login  (usuário logado)
→ 302 Location: /usuario/perfil

GET /login  (admin logado)
→ 302 Location: /admin/dashboard
```

## Notas

- Não há JSON; a rota não grava sessão (apenas a POST `/login` o faz — unidade `autenticar`).
- O redirect para `/admin/dashboard` aponta para rota inexistente no `routes.php` (ADR-010) — em uma reimplementação fiel, reproduzir esse comportamento ou corrigi-lo de forma consciente.
- `autocomplete="off"` presente no formulário.
