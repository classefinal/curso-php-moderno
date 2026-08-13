# User Story — Autenticação

> Fluxo de usuário cobrindo as units: `login/`, `autenticar/`, `logout/`, `login-admin/`, `autenticar-admin/` e `logout-admin/`.

## Narrativa

Um visitante se registra mentalmente como usuário comum (não há tela de cadastro no legado; usuários vêm do seed/migration 8). Ele faz login, navega pela loja, acessa áreas protegidas (perfil) e encerra a sessão. Em paralelo, um administrador usa um login separado para entrar na área administrativa.

## Persona

- **Usuário comum**: cadastrado em `users` com `active = true`.
- **Administrador**: cadastrado em `users` com `admin = true` (ex.: `admin@admin.com` / `admin123` do seed).
- **Visitante**: anônimo — é barrado em `/usuario/perfil`.

## Jornada (usuário comum)

1. Acessa `GET /login` (form com e-mail e senha). 🟢 `src/Controllers/Login/Login.php`
2. Envia `POST /login`. 🟢
3. Sucesso: `$_SESSION['user']` é definida e o usuário é redirecionado para `/` (302). 🟢 `src/Services/Login/LoginService.php`
4. Falha: página de login re-renderizada com 401 e mensagem genérica "Usuário ou senha incorretos"; evento `LoginRecused` grava log. 🟢
5. Usuário já logado que tenta acessar `/login` é redirecionado pelo middleware `preventLogged`. 🟢 `src/Middlewares/preventLogged.php`
6. Para sair: `GET /logout` → 303 para `/` com `$_SESSION['user']` removida. 🟢 `src/Controllers/Login/Login.php`

## Jornada (administrador)

1. Acessa `GET /admin/login` (mesma view de login, título "Login administrativo"). 🟢 `src/Controllers/Admin/Login/AdminLogin.php`
2. Envia `POST /admin/login` com credenciais admin. 🟢
3. Sucesso: `$_SESSION['admin']` definida e redirect 302 para `/admin/dashboard`. 🔴 **Rota inexistente no `routes.php` (ADR-010)**.
4. Falha: 401 com erro; evento `AdminLoginRecused` → log `logs/YYYY-MM-DD-adminLoginErrors.txt`. 🟢
5. Logout: `GET /admin/logout` → remove `$_SESSION['admin']` e 303 `/`; se houver sessão de usuário comum ativa, delega para `/logout`. 🟢

## Regras observadas no código

| Regra | Evidência | Confiança |
|-------|-----------|-----------|
| Senha validada com `password_verify` + hash bcrypt (dummy hash anti-timing) | `src/Services/Login/LoginService.php` | 🟢 |
| Sessões separadas `$_SESSION['user']` / `$_SESSION['admin']` (ADR-007) | `src/Services/Login/LoginService.php:91` | 🟢 |
| Logout sempre 303 See Other (evita re-submissão) | `src/Services/Response.php` | 🟢 |
| Credenciais fixas no seed (admin@admin.com / admin123) | migration 8 | 🟡 |

## Critérios de Aceite

```gherkin
Dado um usuário comum válido e ativo
Quando envia POST /login com credenciais corretas
Então é redirecionado para / com sessão de usuário ativa

Dado um visitante anônimo
Quando acessa /usuario/perfil
Então é redirecionado para /logout pelo middleware auth

Dado um admin válido
Quando envia POST /admin/login com credenciais corretas
Então é redirecionado para /admin/dashboard com sessão de admin ativa (rota pendente — ADR-010)
```

## Métricas de sucesso (sugeridas)

- Taxa de sucesso de login.
- Tempo médio de autenticação.
- Registro de tentativas falhas (logs de evento).

## Pontos de atenção

- 🔴 Schema `users` (email/password) ausente na migration 8 (ADR-008) — a autenticação depende de colunas que a migration não cria.
- 🔴 `/admin/dashboard` inexistente (ADR-010) — após login admin, destino quebrado.
- 🟡 Sessão guarda a linha completa do usuário, incluindo o hash de senha.
