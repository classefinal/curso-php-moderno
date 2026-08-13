# Login (GET /login), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| GET | `/login` | — | HTML do formulário de login | 200 |
| GET | `/login` (com sessão ativa) | sessão `$_SESSION['user']` ou `$_SESSION['admin']` | redirect Location | 302 |

Parâmetros da view (`makeLogin`): `title`, `routes`, `action`.

## Fluxo Principal

1. Requisição GET `/login` → `processRoutes` normaliza a URI (`parse_url` PATH + `rtrim` de `/`) e resolve a rota `login_page`. `src/Services/Router.php:100-102`
2. `requireController('Login/Login')` carrega o controller. `src/Services/Router.php:120`
3. `executeMiddlewares(['preventLogged'], ...)` invoca a pilha de middlewares (LIFO via `array_pop`). `src/Services/Router.php:36-59`, `122-130`
4. `preventLoggedMiddleware`: sem `$_SESSION['admin']` nem `$_SESSION['user']` → `$next()` libera. `src/Middlewares/preventLogged.php:17-29`
5. `makeLogin` monta a view `Login/login` com `title='Login'`, `routes=getMenuItens(...)` e `action='/login'`. `src/Controllers/Login/Login.php:18-27`
6. A view inclui `header.php`, h1, e o `login_form` (action `/login`, método POST, `autocomplete="off"`, campos `email` e `password` required). `src/Pages/Login/login.php`, `src/Components/Login/login_form.php`
7. `$configs['response'](content: $content)` → HTTP 200, `Connection: close`, flush, defer. `src/Services/Response.php:16-35`

## Fluxos Alternativos

- **`$_SESSION['admin']` presente:** middleware faz `redirect('/admin/dashboard', 302)` e não chama `$next()`. `src/Middlewares/preventLogged.php:17-21`
- **`$_SESSION['user']` presente:** middleware faz `redirect('/usuario/perfil', 302)`. `src/Middlewares/preventLogged.php:23-27`
- **Erro de validação no POST:** `validateLogin` (unidade `autenticar`) re-renderiza a mesma view com `error` e HTTP 401 — a view suporta `$error` opcional. `src/Controllers/Login/Login.php:45-52`

## Dependências

- **Router** (`processRoutes`, `executeMiddlewares`), resolução e pipeline de middleware.
- **preventLoggedMiddleware**, guarda de sessão da rota.
- **View** (`createView`), renderização por `extract()` + output buffering.
- **Response** (`response`, `redirect`), envio 200/302 com flush + defer.
- **Functions** (`getMenuItens`), menu de navegação.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Pipeline de middleware com `$next` recursivo (LIFO) | `src/Services/Router.php:36-59` | 🟢 |
| Redirecionamentos separados por papel de sessão | `src/Middlewares/preventLogged.php:17-27` | 🟢 |
| `action` do form injetada pela view (reuso para GET e POST) | `src/Controllers/Login/Login.php:24`, `src/Components/Login/login_form.php:7` | 🟢 |
| Admin logado redirecionado para `/admin/dashboard` — rota planejada (P3) | `src/Middlewares/preventLogged.php:18` | 🟢 |
| Sessões `user`/`admin` independentes (ADR-007) | `src/Middlewares/preventLogged.php` | 🟢 |

## Estado Interno

- Lê `$_SESSION['admin']` e `$_SESSION['user']` (somente leitura). Nenhuma escrita na rota GET.

## Observabilidade

- Nenhum log na rota GET. O fluxo POST dispara o evento `LoginRecused` (unidade `autenticar`) quando a autenticação falha.

## Riscos e Lacunas

- 🟢 `/admin/dashboard` é rota planejada (P3) — página será criada posteriormente; hoje o admin logado cairia em 404 ao acessar `/login`.
- 🟡 Middlewares executam em LIFO (`array_pop`); com pilhas > 1 a ordem pode surpreender, embora nenhuma rota atual tenha mais de um middleware.
