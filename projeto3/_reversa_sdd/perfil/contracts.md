# Perfil (GET /usuario/perfil), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| GET | `/usuario/perfil` | sessão de usuário (`auth`) | — | Página do perfil do usuário autenticado |

> Middleware: `auth` (valida sessão e recarrega usuário do banco).

## Requisição

Sem corpo. Sem query params relevantes.

## Resposta

### 200 OK — usuário autenticado e ativo

- **Corpo:** HTML com:
  - Título `{nome} - Perfil do usuário` e h2 "Perfil do usuário".
  - Alert de erro (quando re-renderizado após falha de POST).
  - Alert verde "Perfil atualizado com sucesso" quando `$_SESSION['profile_updated']` está definida (flag removida pós-resposta).
  - Form `POST /usuario/perfil` com campos:
    - `name` (text, required, autofocus, valor escapado)
    - `email` (email, **disabled**, valor escapado)
    - `old_password`, `new_password`, `password_confirmation` (password, opcionais)

### 303 See Other — não autenticado ou inativo

- **Location:** `/logout`.
- Aplicado quando: sem `$_SESSION['user']['id']`/`active`, ou quando `getUserById` retorna `null`.

## Códigos de status

| Código | Caso |
|--------|------|
| 200 | Sessão válida e usuário ativo no banco |
| 303 | Sem sessão válida ou usuário inexistente/inativo (→ `/logout`) |

## Exemplos

```
GET /usuario/perfil  (sessão válida)
→ 200
  <form method="post" action="/usuario/perfil">
    <input name="name" value="Fulano">
    <input name="email" value="fulano@exemplo.com" disabled>
    <input name="old_password">
    <input name="new_password">
    <input name="password_confirmation">

GET /usuario/perfil  (visitante)
→ 303 Location: /logout
```

## Notas

- Não há JSON. GET é puramente HTML.
- O formulário só é submetido via POST `/usuario/perfil` (unidade `atualizar-perfil`, HTTP 422 em falha).
- O flash de sucesso é transitório: visível apenas no GET seguinte ao POST bem-sucedido.
- Depende de `users.email` (ADR-008): sem a coluna, a view falha ao escapar o e-mail.
