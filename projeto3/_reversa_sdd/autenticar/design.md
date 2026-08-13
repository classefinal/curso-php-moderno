# Autenticar (POST /login), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| POST | `/login` | `email`, `password` (form-urlencoded) | redirect `Location: /usuario/perfil` | 302 |
| POST | `/login` (falha) | `email`, `password` | HTML da página de login com `error` | 401 |

Parâmetros da view em falha (`validateLogin`): `title`, `routes`, `error`, `action`.

## Fluxo Principal

1. POST `/login` → middleware `preventLogged` (se `$_SESSION['admin']` ou `$_SESSION['user']` → redirect imediato). `src/Middlewares/preventLogged.php:17-27`
2. `validateLogin` chama `loginAuthenticate($connection, $eventDispatcher)`. `src/Controllers/Login/Login.php:37`
3. `loginAuthenticate` normaliza `email = strtolower(trim($_POST['email'] ?? ''))` e lê `password = $_POST['password'] ?? ''`. `src/Services/Login/LoginService.php:106-107`
4. `validateLoginInfo`: vazios → "Usuário e senha são obrigatórios."; email inválido → "E-mail inválido."; senha < 8 → "A senha deve ter pelo menos 8 caracteres.". Falha → dispara `LoginRecused` e retorna. `src/Services/Login/LoginService.php:20-37`, `109-120`
5. Consulta `SELECT * FROM users WHERE email = ? AND active = true AND admin = false LIMIT 1` (param `'s'`). `src/Services/Login/LoginService.php:122-131`
6. Sem linhas → dispara `LoginRecused`, executa `password_verify($password, DUMMY_PASSWORD_HASH)` e retorna `DEFAULT_LOGIN_ERROR`. `src/Services/Login/LoginService.php:133-139`
7. Senha verificada com `password_verify($password, $user['password'])`; incorreta → dispara `LoginRecused` e retorna `DEFAULT_LOGIN_ERROR`. `src/Services/Login/LoginService.php:143-149`
8. Sucesso → `$_SESSION['user'] = $user` e retorna `['success' => true, 'error' => 'Um erro foi detectado']`. `src/Services/Login/LoginService.php:151-156`
9. `validateLogin`: sucesso → `$configs['redirect']('/usuario/perfil', 302)`. `src/Controllers/Login/Login.php:39-43`
10. Falha → view `Login/login` com `error` + `$configs['response'](401, $content)`. `src/Controllers/Login/Login.php:45-52`

## Fluxo Alternativo (evento de falha)

1. `eventDispatcher('LoginRecused', ['email' => $email, 'date' => date('Y-m-d H:i:s')])`. `src/Services/Login/LoginService.php:111-114`
2. `createEventDispatcher` resolve o listener `Login/LoginErrorListener::handleLoginErrorEvent`. `src/Configs/events.php:16-18`
3. Listener valida `email`/`date` não vazios e agenda via `$configs['defer'](...)`. `src/Listeners/Login/LoginErrorListener.php:14-20`
4. Após `flush()`/`Connection: close`, o dispatcher roda os deferred: cria `logs/` se preciso e faz `file_put_contents` (append) em `logs/YYYY-MM-DD-loginErrors.txt` com `"{date}: {email}"`. `src/Listeners/Login/LoginErrorListener.php:22-33`, `src/Services/Response.php:34`

## Dependências

- **LoginService** (`loginAuthenticate`, `validateLoginInfo`, consts), lógica de autenticação.
- **DB** (`dbPrepareAndExecute`), consulta tipada por e-mail.
- **EventDispatcher** (`createEventDispatcher`), evento `LoginRecused`.
- **LoginErrorListener** (`handleLoginErrorEvent`), log de falhas pós-resposta.
- **Defer** (`defer`/`dispatcher`), escrita adiada do log.
- **Response** (`redirect`/`response`), saída 302/401.
- **View** (`createView`), re-renderização do form em falha.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Validação de entrada com mensagens específicas | `src/Services/Login/LoginService.php:20-37` | 🟢 |
| Erro genérico para credenciais incorretas | `src/Services/Login/LoginService.php:8-11` | 🟢 |
| Hash dummy anti-timing em usuário inexistente | `src/Services/Login/LoginService.php:133-138` | 🟢 |
| Sessão com a linha inteira do usuário (inclui hash de senha) | `src/Services/Login/LoginService.php:151` | 🟡 |
| Falha HTTP 401 com página re-renderizada | `src/Controllers/Login/Login.php:52` | 🟢 |
| Log de falha via evento + defer (ADR-005) | `src/Listeners/Login/LoginErrorListener.php` | 🟢 |
| Normalização de e-mail com `strtolower` (ADR-007) | `src/Services/Login/LoginService.php:106` | 🟢 |

## Estado Interno

- Escreve `$_SESSION['user']` em sucesso (campos seguros, **sem hash** — P5).
- Evento `LoginRecused` carrega `email` e `date` no args do listener.

## Observabilidade

- Log de falhas em `logs/YYYY-MM-DD-loginErrors.txt`, linha `{date}: {email}`, escrito pós-resposta. `src/Listeners/Login/LoginErrorListener.php`
- Sem log de sucesso de login. `src/Services/Login/LoginService.php`

## Riscos e Lacunas

- 🟢 `users.email`/`users.password` confirmados no schema (P1); a migration 8 deve ser corrigida para incluí-los no CREATE TABLE.
- 🟢 Sessão sem hash de senha (P5) — apenas campos seguros; o middleware `auth` recarrega do banco por `id`.
- 🟡 `DUMMY_PASSWORD_HASH` público e fixo.
- 🟢 String morta `error = 'Um erro foi detectado'` removida no sucesso (P13).
