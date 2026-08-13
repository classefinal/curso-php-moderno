# Login Admin (GET /admin/login), Requisitos

## Visão Geral

Página de autenticação do administrador. Reutiliza a mesma view do login comum (`Login/login`) com título "Login administrativo" e `action` apontando para `/admin/login`. O middleware `preventLogged` bloqueia o acesso quando já existe sessão.

## Responsabilidades

- Renderizar o formulário de login administrativo para não autenticados.
- Impedir login duplo quando há sessão de usuário ou admin.
- Servir de ponto de entrada do fluxo `autenticar-admin` (POST `/admin/login`).

## Regras de Negócio

- Rota `admin_login_page` é **GET** com middleware `preventLogged` 🟢
- Reusa a view `Login/login` e o component `login_form`, trocando `title` e `action` 🟢
- `title = 'Login administrativo'` e `action = '/admin/login'` 🟢
- Sessão de admin ou usuário existente → redirect (mesmo comportamento de `preventLogged` da unit `login`) 🟢
- Admin logado redirecionado para `/admin/dashboard` (rota planejada, página será criada posteriormente — P3) 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Renderizar o formulário admin em `/admin/login` sem sessão | Must | GET `/admin/login` → 200 com form `action=/admin/login` e título "Login administrativo" |
| RF-02 | Redirecionar admin já logado | Must | GET `/admin/login` com `$_SESSION['admin']` → 302 `/admin/dashboard` |
| RF-03 | Redirecionar usuário comum já logado | Must | GET `/admin/login` com `$_SESSION['user']` → 302 `/usuario/perfil` |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Mesmo guard de sessão do login comum (`preventLogged`) | `src/Configs/routes.php:82-94` | 🟢 |
| Disponibilidade | Redireciona para `/admin/dashboard` (rota planejada — P3) | `src/Middlewares/preventLogged.php:18` | 🟢 |
| Segurança | Interpolações de dados na view escapadas com `htmlspecialchars` (P7) | `src/Pages/Login/login.php` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um visitante sem sessão ativa
Quando acessa GET "/admin/login"
Então recebe HTTP 200 com o formulário apontando para POST /admin/login

Dado um admin logado
Quando acessa GET "/admin/login"
Então recebe redirect 302 para "/admin/dashboard"

Dado um usuário comum logado
Quando acessa GET "/admin/login"
Então recebe redirect 302 para "/usuario/perfil"
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Renderizar formulário admin | Must | Acesso administrativo |
| Impedir login duplo | Must | Segurança de sessão |
| Reuso da view de login | Should | Consistência e manutenção |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:72-82` | rota `admin_login_page` (GET `/admin/login`, `makeAdminLogin`, `preventLogged`) | 🟢 |
| `src/Controllers/Admin/Login/AdminLogin.php:18-27` | `makeAdminLogin` | 🟢 |
| `src/Pages/Login/login.php` | view reutilizada | 🟢 |
| `src/Components/Login/login_form.php` | formulário reutilizado | 🟢 |
| `src/Middlewares/preventLogged.php` | `preventLoggedMiddleware` | 🟢 |
