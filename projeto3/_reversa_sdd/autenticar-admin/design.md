# Autenticar Admin (POST /admin/login), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| POST | `/admin/login` | `email`, `password` (form-urlencoded) | redirect `Location: /admin/dashboard` | 302 |
| POST | `/admin/login` (falha) | `email`, `password` | HTML da página de login com `error` | 401 |

Parâmetros da view em falha (`validateAdminLogin`): `title`, `routes`, `error`, `action`.

## Fluxo Principal

1. POST `/admin/login` → middleware `preventLogged` (sessão ativa → redirect imediato). `src/Middlewares/preventLogged.php:17-27`
2. `validateAdminLogin` chama `adminLoginAuthenticate($connection, $eventDispatcher)`. `src/Controllers/Admin/Login/AdminLogin.php:37`
3. `adminLoginAuthenticate` normaliza `email` e lê `password`. `src/Services/Login/LoginService.php:46-47`
4. `validateLoginInfo` (mesma do login comum). Falha → dispara `AdminLoginRecused`, retorna. `src/Services/Login/LoginService.php:49-60`
5. Consulta `SELECT * FROM users WHERE email = ? AND active = true AND admin = true LIMIT 1`. `src/Services/Login/LoginService.php:62-71`
6. Sem linhas → dispara `AdminLoginRecused`, `password_verify` com `DUMMY_PASSWORD_HASH`, retorna `DEFAULT_LOGIN_ERROR`. `src/Services/Login/LoginService.php:73-79`
7. `password_verify($password, $user['password'])`; incorreta → dispara evento, retorna `DEFAULT_LOGIN_ERROR`. `src/Services/Login/LoginService.php:83-89`
8. Sucesso → `$_SESSION['admin'] = $user` (**sem o hash de senha**, P5) e retorno `['success' => true, 'error' => null]` (string morta removida, P13). `src/Services/Login/LoginService.php:91-96`
9. `validateAdminLogin`: sucesso → `$configs['redirect']('/admin/dashboard', 302)`. `src/Controllers/Admin/Login/AdminLogin.php:39-43`
10. Falha → view `Login/login` (`title='Login administrativo'`, `error`, `action='/admin/login'`) com `$configs['response'](401, $content)`. `src/Controllers/Admin/Login/AdminLogin.php:45-52`

## Fluxo Alternativo (evento de falha)

1. `eventDispatcher('AdminLoginRecused', ['email' => $email, 'date' => date('Y-m-d H:i:s')])`. `src/Services/Login/LoginService.php:51-54`
2. Listener `AdminLogin/AdminLoginErrorListener::handleAdminLoginErrorEvent` valida args e agenda via `defer`. `src/Listeners/AdminLogin/AdminLoginErrorListener.php:14-20`
3. Após `flush()`, append em `logs/YYYY-MM-DD-adminLoginErrors.txt` com `"{date}: {email}"`. `src/Listeners/AdminLogin/AdminLoginErrorListener.php:22-33`

## Dependências

- **LoginService** (`adminLoginAuthenticate`, `validateLoginInfo`, consts).
- **DB** (`dbPrepareAndExecute`), consulta por e-mail.
- **EventDispatcher** (`createEventDispatcher`) + **AdminLoginErrorListener**.
- **Defer** (`defer`/`dispatcher`), log pós-resposta.
- **Response** (`redirect`/`response`), saída 302/401.
- **View** (`createView`), re-renderização do form.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Reuso de `validateLoginInfo` e `DEFAULT_LOGIN_ERROR` entre os dois logins | `src/Services/Login/LoginService.php:8-11,20-37` | 🟢 |
| Filtro `admin = true` na consulta separa papéis | `src/Services/Login/LoginService.php:64` | 🟢 |
| Sessão admin separada `$_SESSION['admin']` (ADR-007) | `src/Services/Login/LoginService.php:91` | 🟢 |
| Destino pós-login `/admin/dashboard` — rota planejada, página será criada posteriormente (P3) | `src/Controllers/Admin/Login/AdminLogin.php:40` | 🟢 |
| Evento de falha próprio (`AdminLoginRecused`) com log dedicado | `src/Configs/events.php:13-15` | 🟢 |

## Estado Interno

- Escreve `$_SESSION['admin']` em sucesso (campos seguros, **sem hash** — P5).

## Observabilidade

- Log de falhas em `logs/YYYY-MM-DD-adminLoginErrors.txt`. `src/Listeners/AdminLogin/AdminLoginErrorListener.php`
- Sem log de sucesso.

## Riscos e Lacunas

- 🟢 `/admin/dashboard` é rota planejada — página será criada posteriormente (P3).
- 🟢 `users.email`/`users.password` confirmados no schema (P1); a migration 8 deve ser corrigida para incluí-los no CREATE TABLE.
- 🟡 `DUMMY_PASSWORD_HASH` público (const) com hash de senha conhecida — risco se exposto.
