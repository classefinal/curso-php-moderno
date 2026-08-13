# Autenticar (POST /login), Requisitos

## Visão Geral

Fluxo de autenticação do usuário comum. Valida e-mail/senha, autentica apenas usuários `active=true` e `admin=false`, grava `$_SESSION['user']` em caso de sucesso e redireciona para o perfil. Falhas retornam a página de login com HTTP 401 e disparam o evento `LoginRecused`, que registra o log pós-resposta.

## Responsabilidades

- Validar entrada (`email` obrigatório e válido; `password` obrigatória e ≥ 8 caracteres).
- Consultar usuário ativo, não admin, pelo e-mail (normalizado em minúsculas).
- Verificar senha com `password_verify` (bcrypt).
- Mitigar timing attack com hash dummy quando o usuário não existe.
- Gravar `$_SESSION['user']` e redirecionar para `/usuario/perfil`.
- Em falha, responder 401 com a página de login e disparar o evento `LoginRecused`.

## Regras de Negócio

- E-mail normalizado com `strtolower(trim(...))` antes de validar e consultar 🟢
- Campos vazios → erro "Usuário e senha são obrigatórios." 🟢
- E-mail inválido (`FILTER_VALIDATE_EMAIL`) → erro "E-mail inválido." 🟢
- Senha com menos de 8 caracteres (`/^.{8,}$/`) → erro "A senha deve ter pelo menos 8 caracteres." 🟢
- Consulta exige `active = true AND admin = false` — admin não autentica aqui, e usuário inativo falha 🟢
- Tabela `users` possui `email` e `password` (schema confirmado — P1; a migration 8 deve ser corrigida para incluí-las no CREATE TABLE) 🟢
- Usuário inexistente ou senha incorreta → erro genérico "Usuário ou senha incorretos" (sem vazar qual foi) 🟢
- Usuário inexistente executa `password_verify($password, DUMMY_PASSWORD_HASH)` para uniformizar tempo de resposta 🟢
- Sucesso → `$_SESSION['user']` = linha do usuário **sem o hash de senha** (P5: só campos seguros) 🟢
- Sucesso → redirect `/usuario/perfil` com status **302** 🟢
- Falha → HTTP **401** com a view de login re-renderizada + `error` 🟢
- Retorno de sucesso: `error` é `null` — string morta `"Um erro foi detectado"` removida na reimplementação (P13) 🟢
- Evento `LoginRecused` sempre disparado em falha (validação, usuário inexistente ou senha errada), com `email` e `date` 🟢
- Listener `handleLoginErrorEvent` agenda (defer) a escrita em `logs/YYYY-MM-DD-loginErrors.txt` no formato `{date}: {email}` 🟢
- Middleware `preventLogged`: já logado (admin ou usuário) → redirect antes de autenticar 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Autenticar usuário ativo não admin com credenciais corretas | Must | POST `/login` com credenciais válidas → 302 `/usuario/perfil` e `$_SESSION['user']` definida |
| RF-02 | Rejeitar e-mail inválido com mensagem específica | Must | `email` não-email → 401 + "E-mail inválido." |
| RF-03 | Rejeitar senha curta com mensagem específica | Must | senha < 8 chars → 401 + "A senha deve ter pelo menos 8 caracteres." |
| RF-04 | Rejeitar credenciais erradas com mensagem genérica | Must | usuário inexistente ou senha errada → 401 + "Usuário ou senha incorretos" |
| RF-05 | Rejeitar usuário inativo ou admin | Must | `active=false` ou `admin=true` → 401 + erro genérico |
| RF-06 | Gravar log de falha via evento `LoginRecused` | Should | Falha dispara listener que escreve `logs/{data}-loginErrors.txt` |
| RF-07 | Impedir autenticação já logado | Must | sessão ativa → redirect sem autenticar |
| RF-08 | Não vazar o motivo específico em credenciais erradas | Must | erro idêntico para usuário inexistente e senha errada |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Hash bcrypt (`password_hash`/`password_verify`) com **cost 16** em todo o fluxo (P6) | `src/Migrations/8_create_users_table.php:25`, `src/Services/Login/LoginService.php:83` | 🟢 |
| Segurança | Timing attack mitigado por hash dummy | `src/Services/Login/LoginService.php:133-138` | 🟢 |
| Segurança | `DUMMY_PASSWORD_HASH` público (const) com hash de senha conhecida — risco se exposto | `src/Services/Login/LoginService.php:7` | 🟡 |
| Segurança | Sessão guarda apenas campos seguros, **sem hash de senha** (P5) | `src/Services/Login/LoginService.php:151` | 🟢 |
| Segurança | Registro de log de falha com e-mail do tentante | `src/Listeners/Login/LoginErrorListener.php:28-32` | 🟢 |
| Disponibilidade | Escrita do log adiada para pós-resposta (defer) | `src/Listeners/Login/LoginErrorListener.php:18-33` | 🟢 |
| Operacional | Pasta `logs/` não versionada; criada em runtime com append indefinido (P14) | `src/Listeners/Login/LoginErrorListener.php` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um usuário ativo não admin com senha correta
Quando envia POST /login com email e password válidos
Então recebe 302 Location: /usuario/perfil e $_SESSION['user'] é definida

Dado um usuário inexistente
Quando envia POST /login
Então recebe 401 com "Usuário ou senha incorretos" e um log é registrado em logs/

Dado um e-mail malformado
Quando envia POST /login com email inválido
Então recebe 401 com "E-mail inválido."

Dado um usuário admin ou inativo
Quando envia POST /login
Então recebe 401 com erro genérico "Usuário ou senha incorretos"
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Autenticação válida | Must | Caminho crítico de acesso |
| Erros específicos de validação | Must | Feedback de formulário sem alternativa |
| Erro genérico para credenciais erradas | Must | Segurança (não vazar existência de conta) |
| Log de falhas | Should | Auditoria, não bloqueia o fluxo |
| Hash dummy anti-timing | Should | Postura defensiva de segurança |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:119-130` | rota `login` (POST `/login`, `validateLogin`, `preventLogged`) | 🟢 |
| `src/Controllers/Login/Login.php:35-53` | `validateLogin` | 🟢 |
| `src/Services/Login/LoginService.php:104-157` | `loginAuthenticate` | 🟢 |
| `src/Services/Login/LoginService.php:20-37` | `validateLoginInfo` | 🟢 |
| `src/Services/Login/LoginService.php:7-11` | `DUMMY_PASSWORD_HASH`, `DEFAULT_LOGIN_ERROR` | 🟢 |
| `src/Configs/events.php:16-18` | evento `LoginRecused` | 🟢 |
| `src/Listeners/Login/LoginErrorListener.php` | `handleLoginErrorEvent` | 🟢 |
| `src/Middlewares/preventLogged.php` | `preventLoggedMiddleware` | 🟢 |
