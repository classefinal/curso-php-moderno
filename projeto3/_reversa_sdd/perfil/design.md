# Perfil (GET /usuario/perfil), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| GET | `/usuario/perfil` | sessão válida (`$_SESSION['user']`) | HTML do perfil | 200 |
| GET | `/usuario/perfil` (sem sessão) | — | redirect `Location: /logout` | 303 |

Parâmetros da view (`viewProfile`): `title`, `user`, `routes`. Flash lido da própria sessão na view.

## Fluxo Principal

1. GET `/usuario/perfil` → rota `user_profile`, middleware `['auth']`. `src/Configs/routes.php:143-154`
2. `authMiddleware`: sem `$_SESSION['user']['id']` ou `active` vazio → `redirect('/logout', 303)`. `src/Middlewares/auth.php:21-25`
3. `getUserById($connection, $_SESSION['user']['id'])` — `SELECT * FROM users WHERE id = ? AND active = true LIMIT 1`. `src/Services/Users/UsersService.php:13-31`
4. Usuário não encontrado → `redirect('/logout', 303)`. `src/Middlewares/auth.php:27-32`
5. `$configs['user'] = $user` e `$next()`. `src/Middlewares/auth.php:35-37`
6. `viewProfile` lê `$configs['user']` e monta a view `Users/profile` com `title = "{name} - Perfil do usuário"`, `user`, `routes`. `src/Controllers/Users/Users.php:18-27`
7. View renderiza h2, alertas (erro/flash), e o form `POST /usuario/perfil` com nome (escaped), e-mail (disabled, escaped) e 3 campos de senha. `src/Pages/Users/profile.php`
8. `viewProfile` agenda via `defer` o `unset($_SESSION['profile_updated'])`. `src/Controllers/Users/Users.php:28-32`
9. `$configs['response'](content: $content)` → 200, flush, roda deferred (limpa o flash). `src/Services/Response.php:16-35`

## Fluxos Alternativos

- **Flash de sucesso:** a view checa `!empty($_SESSION['profile_updated'])` e exibe alert verde; a limpeza acontece pós-resposta. `src/Pages/Users/profile.php:26-28`, `src/Controllers/Users/Users.php:28-32`
- **Erro de atualização:** `updateProfile` (unidade `atualizar-perfil`) re-renderiza a mesma view com `error` e HTTP 422. `src/Controllers/Users/Users.php:53-60`
- **Usuário inativo:** `getUserById` filtra `active = true`; retorna null → redirect `/logout`. `src/Services/Users/UsersService.php:17`

## Dependências

- **Router** (`processRoutes`, `executeMiddlewares`), resolução + middleware.
- **authMiddleware**, validação da sessão e recarga do usuário.
- **UsersService** (`getUserById`), leitura do usuário ativo.
- **DB** (`dbPrepareAndExecute`), consulta tipada.
- **View** (`createView`), renderização.
- **Response** (`response`, `redirect`), saída 200/303.
- **Defer** (`defer`), limpeza do flash pós-resposta.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Recarga do usuário do banco a cada request (sessão não é fonte de verdade) | `src/Middlewares/auth.php:27` | 🟢 |
| Flash `profile_updated` limpo via defer pós-resposta | `src/Controllers/Users/Users.php:28-32` | 🟢 |
| View lê a sessão diretamente para o flash | `src/Pages/Users/profile.php:26` | 🟢 |
| E-mail `disabled` (não editável) e nome editável | `src/Pages/Users/profile.php:30-38` | 🟢 |
| Escape com `htmlspecialchars` (único lugar com escape em views) | `src/Pages/Users/profile.php:33,37` | 🟢 |

## Estado Interno

- Lê `$_SESSION['user']['id']`/`active` (middleware) e `$_SESSION['profile_updated']` (view).
- Remove `$_SESSION['profile_updated']` de forma adiada.
- `$configs['user']` é definida pelo middleware (linha completa do banco, inclui `password`).

## Observabilidade

- Nenhum log específico desta página.

## Riscos e Lacunas

- 🟢 View depende de `users.email` — coluna confirmada no schema (P1); a migration 8 deve ser corrigida para incluí-la no CREATE TABLE.
- 🟢 `$configs['user']`/sessão sem hash de senha (P5) — a view recebe apenas campos seguros.
- 🟡 E-mail não editável: mudanças de e-mail exigiriam fluxo próprio (não existe).
