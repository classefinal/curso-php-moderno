# Atualizar Perfil (POST /usuario/perfil), Requisitos

## Visão Geral

Processa o POST do formulário de perfil do usuário comum autenticado. Altera o nome (obrigatório) e, opcionalmente, a senha (exige senha atual + confirmação). Em sucesso, atualiza a sessão e redireciona para o perfil com flash de sucesso; em falha, re-renderiza a página com HTTP 422 e a mensagem de erro.

## Responsabilidades

- Validar e atualizar o `name` do usuário.
- Validar e alterar a senha apenas quando `new_password` for preenchido.
- Gravar hash bcrypt da nova senha (nunca em texto puro).
- Atualizar `$_SESSION['user']` (sem o hash de senha) e marcar flash `profile_updated`.
- Redirecionar 302 `/usuario/perfil` no sucesso; 422 com a view re-renderizada na falha.
- Remover o hash de senha da sessão ao atualizar (`setUpdatedUserIntoSession`).

## Regras de Negócio

- Nome: `strip_tags(trim($_POST['name']))`, obrigatório com 3–255 caracteres 🟢
- Nome inválido → 422 `'O nome deve ter entre 3 e 255 caracteres'` 🟢
- Se `new_password` vazio → atualiza **somente** o nome (`UPDATE users SET name = ?`) 🟢
- Se `new_password` preenchido → valida senha antiga via `password_verify($old_password, $user['password'])` 🟢
- Senha atual incorreta → 422 `'Senha atual incorreta'` 🟢
- `new_password` vazio (mas campo existente) → 422 `'Preencha a senha'` 🟢
- `new_password !== password_confirmation` → 422 `'A confirmaçao de senha deve ser igual a nova senha'` 🟢
- `strlen(new_password) < 8` → 422 `'A senha deve ter pelo menos 8 caracteres'` 🟢
- Com senha válida → `password_hash($new_password, PASSWORD_BCRYPT)` e `UPDATE users SET name = ?, password = ?` 🟢
- Sucesso → `unset($user['password'])`, `$_SESSION['user'] = $user`, `$_SESSION['profile_updated'] = true` 🟢
- Sucesso → redirect `302` para `/usuario/perfil` 🟢
- E-mail nunca é alterado por esta rota 🟢
- Depende de `users.password` — coluna **confirmada no schema** (P1); a migration 8 deve ser corrigida para incluí-la no CREATE TABLE 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Atualizar o nome | Must | POST com nome válido → 302 e `users.name` atualizado |
| RF-02 | Atualizar nome + senha | Must | POST com `new_password` válido → 302 e `users.password` re-hash |
| RF-03 | Não alterar senha sem pedido | Must | `new_password` vazio → apenas `UPDATE ... SET name` |
| RF-04 | Validar senha atual | Must | `old_password` errada → 422 `'Senha atual incorreta'` |
| RF-05 | Rejeitar confirmação divergente | Must | `new_password !== password_confirmation` → 422 |
| RF-06 | Rejeitar senha curta | Must | `< 8` caracteres → 422 |
| RF-07 | Atualizar sessão e flash | Must | sucesso → sessão sem hash + `profile_updated` |
| RF-08 | Rejeitar nome inválido | Must | fora de 3–255 → 422 |
| RF-09 | Sanitizar nome | Must | `strip_tags` no nome recebido |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Senha armazenada apenas como hash bcrypt | `src/Services/Users/UsersService.php:122` | 🟢 |
| Segurança | Sessão não guarda hash após atualização | `src/Services/Users/UsersService.php:66-72` | 🟢 |
| Segurança | Nome sanitizado com `strip_tags` | `src/Services/Users/UsersService.php:82` | 🟢 |
| Segurança | Acesso restrito pelo middleware `auth` | `src/Configs/routes.php:143-154` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um usuário autenticado
Quando envia POST "/usuario/perfil" com name válido e sem new_password
Então recebe 302 com Location "/usuario/perfil" e $_SESSION['profile_updated'] é definida

Dado um usuário autenticado
Quando envia POST "/usuario/perfil" com new_password e password_confirmation iguais (>= 8 chars)
Então recebe 302 e users.password é atualizado com hash bcrypt

Dado um usuário autenticado
Quando envia POST "/usuario/perfil" com new_password inválida
Então recebe 422 com a mensagem de erro e o perfil é re-renderizado
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Atualizar nome | Must | Núcleo do formulário |
| Alteração de senha condicional | Must | Regra central |
| Validações com 422 | Must | Feedback obrigatório |
| Sanitização do nome | Should | Higiene de entrada |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:155-166` | rota `user_profile_update` (POST `/usuario/perfil`, `updateProfile`, middleware `auth`) | 🟢 |
| `src/Controllers/Users/Users.php:43-61` | `updateProfile` | 🟢 |
| `src/Services/Users/UsersService.php:79-142` | `updateUserProfile` | 🟢 |
| `src/Services/Users/UsersService.php:37-60` | `validateUpdateUserPassword` | 🟢 |
| `src/Services/Users/UsersService.php:66-72` | `setUpdatedUserIntoSession` | 🟢 |
| `src/Pages/Users/profile.php` | view re-renderizada no 422 | 🟢 |
