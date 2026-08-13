# Autenticar Admin (POST /admin/login), Requisitos

## Visão Geral

Fluxo de autenticação do administrador. Espelha o login comum, mas autentica apenas usuários `active=true` e `admin=true`, gravando `$_SESSION['admin']`. Falhas respondem 401 com a página de login e disparam o evento `AdminLoginRecused`, que registra log pós-resposta.

## Responsabilidades

- Validar e-mail/senha (mesmas regras do login comum).
- Consultar usuário ativo **admin** pelo e-mail.
- Verificar senha com `password_verify`.
- Mitigar timing attack com hash dummy.
- Gravar `$_SESSION['admin']` e redirecionar para `/admin/dashboard`.
- Em falha, 401 + evento `AdminLoginRecused`.

## Regras de Negócio

- E-mail normalizado com `strtolower(trim(...))` 🟢
- Mesmas mensagens de validação do login comum (obrigatórios, e-mail inválido, senha < 8) 🟢
- Consulta exige `active = true AND admin = true` — usuário comum **não** autentica aqui 🟢
- Usuário inexistente ou senha errada → erro genérico "Usuário ou senha incorretos" 🟢
- Usuário inexistente executa `password_verify` contra `DUMMY_PASSWORD_HASH` (anti-timing) 🟢
- Tabela `users` possui `email` e `password` (schema confirmado — P1; a migration 8 deve ser corrigida para incluí-las no CREATE TABLE) 🟢
- Sucesso → `$_SESSION['admin']` = linha do usuário **sem o hash de senha** (P5: só campos seguros) 🟢
- Sucesso → redirect **302** `/admin/dashboard` (rota planejada, página será criada posteriormente — P3) 🟢
- Falha → HTTP **401** com view `Login/login` + `error` 🟢
- Evento `AdminLoginRecused` disparado em toda falha, com `email` e `date` 🟢
- Listener agenda (defer) escrita em `logs/YYYY-MM-DD-adminLoginErrors.txt`, linha `{date}: {email}` 🟢
- Retorno de sucesso: `error` é `null` — string morta `"Um erro foi detectado"` removida na reimplementação (P13) 🟢
- Seed admin mantido: `admin@admin.com` / senha `admin123` (credenciais padrão conhecidas — decisão P4) 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Autenticar admin ativo com credenciais corretas | Must | POST `/admin/login` válido → 302 `/admin/dashboard` e `$_SESSION['admin']` definida |
| RF-02 | Rejeitar usuário comum (admin=false) | Must | `admin=false` → 401 com erro genérico |
| RF-03 | Rejeitar admin inativo | Must | `active=false` → 401 com erro genérico |
| RF-04 | Rejeitar credenciais erradas com erro genérico | Must | usuário inexistente ou senha errada → 401 "Usuário ou senha incorretos" |
| RF-05 | Validar entrada com mensagens específicas | Must | vazios/e-mail inválido/senha curta → 401 com mensagem específica |
| RF-06 | Gravar log de falha via `AdminLoginRecused` | Should | falha escreve `logs/{data}-adminLoginErrors.txt` |
| RF-07 | Impedir autenticação já logado | Must | sessão ativa → redirect antes de autenticar |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Hash bcrypt + dummy anti-timing, **cost 16 em todo o fluxo** (P6) | `src/Services/Login/LoginService.php:44-97` | 🟢 |
| Segurança | Sessão admin separada da de usuário (ADR-007) | `src/Services/Login/LoginService.php:91` | 🟢 |
| Segurança | Sessão guarda apenas campos seguros, **sem hash de senha** (P5) | `src/Services/Login/LoginService.php:91` | 🟢 |
| Segurança | Log de falha registra e-mail tentado | `src/Listeners/AdminLogin/AdminLoginErrorListener.php:28-32` | 🟢 |
| Disponibilidade | Log escrito pós-resposta (defer) | `src/Listeners/AdminLogin/AdminLoginErrorListener.php:18-33` | 🟢 |
| Operacional | Pasta `logs/` não versionada; criada em runtime com append indefinido (P14) | `src/Listeners/AdminLogin/AdminLoginErrorListener.php` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um admin ativo com senha correta
Quando envia POST /admin/login com email e password válidos
Então recebe 302 Location: /admin/dashboard e $_SESSION['admin'] é definida

Dado um usuário comum (admin=false)
Quando envia POST /admin/login
Então recebe 401 com "Usuário ou senha incorretos"

Dado um admin inexistente
Quando envia POST /admin/login
Então recebe 401 com erro genérico e log em logs/adminLoginErrors
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Autenticação admin | Must | Acesso administrativo |
| Rejeitar usuário comum/inativo | Must | Separação de papéis |
| Erros específicos de validação | Must | Feedback de formulário |
| Erro genérico para credenciais | Must | Não vazar existência de conta |
| Log de falhas | Should | Auditoria |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:83-94` | rota `admin_login` (POST `/admin/login`, `validateAdminLogin`, `preventLogged`) | 🟢 |
| `src/Controllers/Admin/Login/AdminLogin.php:35-53` | `validateAdminLogin` | 🟢 |
| `src/Services/Login/LoginService.php:44-97` | `adminLoginAuthenticate` | 🟢 |
| `src/Services/Login/LoginService.php:20-37` | `validateLoginInfo` | 🟢 |
| `src/Configs/events.php:13-15` | evento `AdminLoginRecused` | 🟢 |
| `src/Listeners/AdminLogin/AdminLoginErrorListener.php` | `handleAdminLoginErrorEvent` | 🟢 |
| `src/Middlewares/preventLogged.php` | `preventLoggedMiddleware` | 🟢 |
