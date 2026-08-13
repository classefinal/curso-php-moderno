# Atualizar Perfil (POST /usuario/perfil), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| POST | `/usuario/perfil` | `name`, `old_password`, `new_password`, `password_confirmation` | redirect `Location: /usuario/perfil` | 302 |
| POST | `/usuario/perfil` (falha) | idem | HTML do perfil com `error` | 422 |

Parâmetros da view em falha: `title`, `user`, `error`, `routes`.

## Fluxo Principal (sucesso)

1. POST `/usuario/perfil` → middleware `auth` valida/recarrega o usuário. `src/Configs/routes.php:155-166`
2. `updateProfile` chama `updateUserProfile($connection, $configs['user'])`. `src/Controllers/Users/Users.php:45`
3. `$name = strip_tags(trim($_POST['name'] ?? ''))`. `src/Services/Users/UsersService.php:82`
4. Nome fora de 3–255 → retorna erro `'O nome deve ter entre 3 e 255 caracteres'`. `src/Services/Users/UsersService.php:84-90`
5. `new_password` vazio → `UPDATE users SET name = ? WHERE id = ?`. `src/Services/Users/UsersService.php:92-100`
6. Com `new_password` → `validateUpdateUserPassword($user)` (senha atual + confirmação + tamanho). `src/Services/Users/UsersService.php:112-120`, `:37-60`
7. `$hash = password_hash($new_password, PASSWORD_BCRYPT)`; `UPDATE users SET name = ?, password = ? WHERE id = ?`. `src/Services/Users/UsersService.php:122-132`
8. `setUpdatedUserIntoSession($user)` → `unset($user['password'])`, `$_SESSION['user'] = $user`, `$_SESSION['profile_updated'] = true`. `src/Services/Users/UsersService.php:66-72`
9. `updateProfile` → `$configs['redirect']('/usuario/perfil', 302)`. `src/Controllers/Users/Users.php:47-51`

## Fluxo de Falha

1. Qualquer validação falha → `updateUserProfile` retorna `['success' => false, 'error' => $msg, 'user' => $user]`.
2. `updateProfile` re-renderiza `Users/profile` com `title`, `user` (valor original), `error`, `routes`. `src/Controllers/Users/Users.php:53-58`
3. `$configs['response'](422, $content)` — HTML 422, flash `profile_updated` **não** é definido. `src/Controllers/Users/Users.php:60`

## Fluxos Alternativos

- **Só nome (sem senha):** nenhum `password_hash` é executado; apenas `UPDATE ... SET name`. `src/Services/Users/UsersService.php:92-110`
- **Sessão após sucesso:** o hash é removido da sessão, mas o próximo GET `/usuario/perfil` recarrega a linha completa do banco via middleware (hash volta a trafegar até a view). `src/Services/Users/UsersService.php:68`, `src/Middlewares/auth.php:27`

## Dependências

- **Router/Middleware** (`auth`), acesso restrito.
- **UsersService** (`updateUserProfile`, `validateUpdateUserPassword`, `setUpdatedUserIntoSession`).
- **DB** (`dbPrepareAndExecute`), UPDATEs tipados.
- **View** (`createView`), re-renderização no 422.
- **Response** (`redirect`, `response`), 302/422.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Alteração de senha condicional (só se `new_password` preenchido) | `src/Services/Users/UsersService.php:92` | 🟢 |
| Validação da senha atual via `password_verify` | `src/Services/Users/UsersService.php:43` | 🟢 |
| Hash bcrypt com cost padrão (`PASSWORD_BCRYPT`), diverge do cost 16 do seed | `src/Services/Users/UsersService.php:122` | 🟡 |
| Sessão sem hash de senha (`unset`) | `src/Services/Users/UsersService.php:68` | 🟢 |
| Flash `profile_updated` para feedback no GET seguinte | `src/Services/Users/UsersService.php:71` | 🟢 |
| 422 na falha em vez de redirect | `src/Controllers/Users/Users.php:60` | 🟢 |

## Estado Interno

- Escreve `users.name` e, condicionalmente, `users.password`.
- Escreve `$_SESSION['user']` (sem hash) e `$_SESSION['profile_updated'] = true`.

## Observabilidade

- Nenhum log nesta rota (nem sucesso nem falha).

## Riscos e Lacunas

- 🟢 `users.password`/`users.email` confirmados no schema (P1); a migration 8 deve ser corrigida para incluí-los no CREATE TABLE.
- 🟡 Cost de bcrypt **16 em todo o fluxo** (P6) — seed/dummy/hash de atualização devem usar o mesmo cost.
- 🟡 No 422 os campos digitados não são repopulados (a view usa `$user['name']` do banco; senhas sempre vazias).
- 🟢 Sem transação — UPDATEs são atômicos por serem de uma linha.
