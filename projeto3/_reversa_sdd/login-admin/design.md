# Login Admin (GET /admin/login), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| GET | `/admin/login` | — | HTML do formulário admin | 200 |
| GET | `/admin/login` (sessão ativa) | sessão `user`/`admin` | redirect | 302 |

Parâmetros da view (`makeAdminLogin`): `title`, `routes`, `action`.

## Fluxo Principal

1. GET `/admin/login` → rota `admin_login_page` resolvida. `src/Configs/routes.php:72-82`
2. `executeMiddlewares(['preventLogged'], ...)`; sem sessão → `$next()`. `src/Middlewares/preventLogged.php:29`
3. `makeAdminLogin` monta a view `Login/login` com `title='Login administrativo'`, `routes` e `action='/admin/login'`. `src/Controllers/Admin/Login/AdminLogin.php:18-27`
4. View reutilizada inclui `header.php`, h1, `login_form` (POST `/admin/login`). `src/Pages/Login/login.php`, `src/Components/Login/login_form.php`
5. `$configs['response'](content: $content)` → 200. `src/Services/Response.php:16-35`

## Fluxos Alternativos

- **`$_SESSION['admin']`:** redirect `/admin/dashboard` (302). `src/Middlewares/preventLogged.php:17-21`
- **`$_SESSION['user']`:** redirect `/usuario/perfil` (302). `src/Middlewares/preventLogged.php:23-27`
- **Erro no POST:** `validateAdminLogin` (unidade `autenticar-admin`) re-renderiza com `error` e 401. `src/Controllers/Admin/Login/AdminLogin.php:45-52`

## Dependências

- **Router** (`processRoutes`, `executeMiddlewares`), resolução + pipeline.
- **preventLoggedMiddleware**, guarda de sessão.
- **View** (`createView`) e **Response** (`response`), renderização/envio.
- **Functions** (`getMenuItens`), menu.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Reuso da view `Login/login` para o fluxo admin (parametrização por `title`/`action`) | `src/Controllers/Admin/Login/AdminLogin.php:20-24` | 🟢 |
| Mesmo middleware `preventLogged` dos dois papéis | `src/Configs/routes.php:82`, `108-118` | 🟢 |
| Admin logado redirecionado para `/admin/dashboard` — rota planejada (P3) | `src/Middlewares/preventLogged.php:18` | 🟢 |

## Estado Interno

- Somente leitura de `$_SESSION['admin']`/`$_SESSION['user']` na rota GET.

## Observabilidade

- Nenhum log na rota GET. Falhas do POST disparam `AdminLoginRecused` (unidade `autenticar-admin`).

## Riscos e Lacunas

- 🟢 `/admin/dashboard` é rota planejada (P3) — página será criada posteriormente; mesmo comportamento da unit `login`.
- 🟡 Reuso de view acopla o layout do login admin ao login comum (mudanças no form afetam ambos).
