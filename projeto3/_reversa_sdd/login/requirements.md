# Login (GET /login), Requisitos

## Visão Geral

Página pública de autenticação do usuário comum. Renderiza o formulário de login (e-mail + senha). Quando já existe sessão ativa (usuário ou admin), o middleware `preventLogged` redireciona para a área correspondente, impedindo login duplo.

## Responsabilidades

- Renderizar o formulário de login para usuário não autenticado.
- Impedir acesso à página quando já existe sessão (`preventLogged`).
- Servir de ponto de entrada do fluxo `autenticar` (POST `/login`, unidade própria).

## Regras de Negócio

- Rota `login_page` exige método **GET** e middleware `preventLogged` 🟢
- Se `$_SESSION['admin']` existir → redireciona para `/admin/dashboard` (rota planejada, página a ser criada — P3) 🟢
- Se `$_SESSION['user']` existir → redireciona para `/usuario/perfil` 🟢
- Sem sessão → `$next()` renderiza a página 🟢
- Formulário aponta para POST `/login` com `action` passado pela view 🟢
- Campos do formulário: `email` (type email, `required`, autofocus) e `password` (type password, `required`), com `autocomplete="off"` 🟢
- Página exibe bloco de erro quando `$error` é passado (usado no fluxo POST; na GET nunca vem) 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Renderizar o formulário de login ao acessar `/login` sem sessão | Must | GET `/login` → 200 com form (email, senha, botão "Entrar") |
| RF-02 | Redirecionar usuário comum já logado | Must | GET `/login` com `$_SESSION['user']` → 302 `/usuario/perfil` |
| RF-03 | Redirecionar admin já logado | Must | GET `/login` com `$_SESSION['admin']` → 302 `/admin/dashboard` |
| RF-04 | Exibir o menu de navegação na página | Should | `routes` injetado via `getMenuItens` |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Sessões separadas por papel (`user`/`admin`) impedem coexistência no mesmo fluxo | `src/Middlewares/preventLogged.php:17-27` | 🟢 |
| Segurança | `autocomplete="off"` no formulário | `src/Components/Login/login_form.php:7-14` | 🟢 |
| Disponibilidade | Redireciona para `/admin/dashboard` quando admin logado acessa `/login` — rota planejada, página será criada posteriormente (P3) | `src/Middlewares/preventLogged.php:18`, `src/Configs/routes.php` | 🟢 |
| Segurança | Interpolações de dados na view escapadas com `htmlspecialchars` (P7) | `src/Pages/Login/login.php:16-18` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um visitante sem sessão ativa
Quando acessa GET "/login"
Então recebe HTTP 200 com o formulário de login

Dado um usuário com $_SESSION['user'] ativa
Quando acessa GET "/login"
Então recebe redirect 302 para "/usuario/perfil"

Dado um admin com $_SESSION['admin'] ativa
Quando acessa GET "/login"
Então recebe redirect 302 para "/admin/dashboard"
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Renderizar formulário | Must | Caminho crítico de autenticação |
| Impedir login duplo | Must | Segurança de sessão sem alternativa |
| Redirecionamento do admin | Must | Comportamento observado no middleware |
| Exibir menu | Should | Apresentação, não bloqueia autenticação |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:108-118` | rota `login_page` (GET `/login`, `makeLogin`, middleware `preventLogged`) | 🟢 |
| `src/Controllers/Login/Login.php:18-27` | `makeLogin` | 🟢 |
| `src/Pages/Login/login.php` | view da página | 🟢 |
| `src/Components/Login/login_form.php` | formulário | 🟢 |
| `src/Middlewares/preventLogged.php` | `preventLoggedMiddleware` | 🟢 |
| `src/Functions/Functions.php` | `getMenuItens` | 🟢 |
