# Logout Admin (GET /admin/logout), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| GET | `/admin/logout` | sessão (`$_SESSION['admin']` ou `$_SESSION['user']`) | redirect | 303 |

## Fluxo Principal

1. GET `/admin/logout` → rota `admin_logout` (`logoutAdminLogin`), sem middlewares. `src/Configs/routes.php:95-106`
2. `logoutAdminLogin` verifica `$_SESSION['user']`. `src/Controllers/Admin/Login/AdminLogin.php:63`
3. Se usuário comum → `$configs['redirect']('/logout', 303)` e encerra. `src/Controllers/Admin/Login/AdminLogin.php:64-67`
4. Senão → `unset($_SESSION['admin'])`. `src/Controllers/Admin/Login/AdminLogin.php:69`
5. `$configs['redirect']('/', 303)` — `ob_clean`, `Location: /`, flush, dispatcher de deferred. `src/Controllers/Admin/Login/AdminLogin.php:71`, `src/Services/Response.php:37-46`

## Fluxos Alternativos

- **Sem nenhuma sessão:** `unset` é no-op e o redirect para `/` segue normalmente.
- **Usuário comum em `/admin/logout`:** delega para `/logout`, que faz `unset($_SESSION['user'])` (unidade `logout`).

## Dependências

- **Router** (`processRoutes`), resolução da rota.
- **Response** (`redirect`), emissão do 303 + flush + defer.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Logout por `unset` da chave `admin` | `src/Controllers/Admin/Login/AdminLogin.php:69` | 🟢 |
| Delegação do usuário comum para `/logout` | `src/Controllers/Admin/Login/AdminLogin.php:63-67` | 🟢 |
| 303 See Other em todos os desfechos | `src/Controllers/Admin/Login/AdminLogin.php:64,71` | 🟢 |

## Estado Interno

- Remove `$_SESSION['admin']`. Nunca toca `$_SESSION['user']` nesta rota.

## Observabilidade

- Nenhum log no logout de admin.

## Riscos e Lacunas

- 🟡 Não invalida o ID da sessão (`session_regenerate_id`) nem apaga o cookie.
- 🟢 Comportamento estável; sem lacunas humanas pendentes.
