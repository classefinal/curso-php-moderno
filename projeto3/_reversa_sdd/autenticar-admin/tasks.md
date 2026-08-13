# Autenticar Admin (POST /admin/login), Tarefas de Implementação

## Pré-requisitos

- [ ] Tabela `users` com `email`/`password` e seed admin (ver ADR-008)
- [ ] EventDispatcher com listeners em arquivo operacional
- [ ] `defer`/dispatcher pós-resposta disponível

## Tarefas

- [ ] T-01, Implementar `adminLoginAuthenticate` normalizando e-mail e consultando `SELECT * FROM users WHERE email = ? AND active = true AND admin = true LIMIT 1`
  - Origem no legado: `src/Services/Login/LoginService.php:44-71`
  - Critério de pronto: consulta tipada `'s'`; só admin ativo autentica
  - Confiança: 🟢

- [ ] T-02, Implementar falhas com `DEFAULT_LOGIN_ERROR` + `password_verify` contra `DUMMY_PASSWORD_HASH` para usuário inexistente
  - Origem no legado: `src/Services/Login/LoginService.php:73-89`
  - Critério de pronto: erro genérico e tempo uniforme
  - Confiança: 🟢

- [ ] T-03, Implementar sucesso gravando `$_SESSION['admin']` e retornando `success=true`
  - Origem no legado: `src/Services/Login/LoginService.php:91-96`
  - Critério de pronto: sessão admin definida
  - Confiança: 🟢

- [ ] T-04, Implementar `validateAdminLogin` com redirect 302 `/admin/dashboard` no sucesso e 401 com view `Login/login` na falha
  - Origem no legado: `src/Controllers/Admin/Login/AdminLogin.php:35-53`
  - Critério de pronto: 302 no sucesso; 401 com erro na falha
  - Confiança: 🟢

- [ ] T-05, Registrar o evento `AdminLoginRecused` mapeando `AdminLogin/AdminLoginErrorListener::handleAdminLoginErrorEvent`
  - Origem no legado: `src/Configs/events.php:13-15`
  - Critério de pronto: falha admin dispara o listener
  - Confiança: 🟢

- [ ] T-06, Implementar `handleAdminLoginErrorEvent` com defer para append em `logs/YYYY-MM-DD-adminLoginErrors.txt` (`{date}: {email}`)
  - Origem no legado: `src/Listeners/AdminLogin/AdminLoginErrorListener.php`
  - Critério de pronto: arquivo criado/appendado pós-resposta
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Happy path admin → 302 `/admin/dashboard` + sessão `admin`
- [ ] TT-02, Usuário comum (`admin=false`) → 401
- [ ] TT-03, Admin inexistente → 401 genérico + log
- [ ] TT-04, Senha errada → 401 genérico
- [ ] TT-05, Validação (vazios/e-mail/senha) → 401 com mensagens específicas
- [ ] TT-06, Log de falha em `logs/adminLoginErrors`

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Garantir seed admin (`admin@admin.com` / `admin123`, `admin=true`) com colunas `email`/`password` (ADR-008)

## Ordem Sugerida

1. T-01 → T-02 → T-03 (serviço) → T-04 (controller)
2. T-05/T-06 (evento de log)
3. Testes TT-01–TT-06; migração TM-01 antes de testes de sucesso

## Lacunas Pendentes (🔴)

- 🔴 `/admin/dashboard` inexistente (ADR-010) — confirmar destino pós-login admin (unit `login-admin`, Q1/Q4).
- 🔴 Schema de `users` (email/password) — ver ADR-008.
