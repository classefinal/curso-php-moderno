# Autenticar (POST /login), Tarefas de Implementação

## Pré-requisitos

- [ ] Tabela `users` com `email` e `password` (ver ADR-008 — schema real em produção precisa ser confirmado)
- [ ] EventDispatcher com listeners em arquivo operacional
- [ ] `defer`/dispatcher pós-resposta disponível

## Tarefas

- [ ] T-01, Implementar `validateLoginInfo` com mensagens específicas (obrigatórios, e-mail válido, senha ≥ 8)
  - Origem no legado: `src/Services/Login/LoginService.php:20-37`
  - Critério de pronto: retorna `LoginInfo` com erro correto por caso
  - Confiança: 🟢

- [ ] T-02, Implementar `loginAuthenticate` normalizando e-mail (`strtolower(trim)`), lendo password do POST e consultando `SELECT * FROM users WHERE email = ? AND active = true AND admin = false LIMIT 1`
  - Origem no legado: `src/Services/Login/LoginService.php:104-131`
  - Critério de pronto: consulta tipada `'s'`; admin e inativos não autenticam
  - Confiança: 🟢

- [ ] T-03, Implementar mitigação de timing attack: usuário inexistente executa `password_verify` contra `DUMMY_PASSWORD_HASH` e retorna `DEFAULT_LOGIN_ERROR`
  - Origem no legado: `src/Services/Login/LoginService.php:133-139`
  - Critério de pronto: mesmo erro e tempo similar ao de senha errada
  - Confiança: 🟢

- [ ] T-04, Implementar verificação de senha com `password_verify` e gravação de `$_SESSION['user']`
  - Origem no legado: `src/Services/Login/LoginService.php:141-156`
  - Critério de pronto: senha correta → sessão definida e `success=true`
  - Confiança: 🟢

- [ ] T-05, Implementar `validateLogin` com redirect 302 `/usuario/perfil` no sucesso e HTTP 401 com a view `Login/login` + `error` na falha
  - Origem no legado: `src/Controllers/Login/Login.php:35-53`
  - Critério de pronto: 302 no sucesso; 401 com mensagem na falha
  - Confiança: 🟢

- [ ] T-06, Registrar o evento `LoginRecused` mapeando `Login/LoginErrorListener::handleLoginErrorEvent`
  - Origem no legado: `src/Configs/events.php:16-18`
  - Critério de pronto: falha de login dispara o listener
  - Confiança: 🟢

- [ ] T-07, Implementar `handleLoginErrorEvent` validando args e agendando via `defer` a escrita (append) em `logs/YYYY-MM-DD-loginErrors.txt` com `"{date}: {email}"`
  - Origem no legado: `src/Listeners/Login/LoginErrorListener.php`
  - Critério de pronto: arquivo criado/appendado com a linha do e-mail após a resposta
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Happy path: credenciais válidas → 302 `/usuario/perfil` + sessão
- [ ] TT-02, Usuário inexistente → 401 com erro genérico
- [ ] TT-03, Senha errada → 401 com erro genérico
- [ ] TT-04, E-mail inválido → 401 "E-mail inválido."
- [ ] TT-05, Senha curta → 401 "A senha deve ter pelo menos 8 caracteres."
- [ ] TT-06, Usuário inativo ou admin → 401
- [ ] TT-07, Log de falha criado em `logs/` com o e-mail tentado

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Restaurar/garantir `users.email` e `users.password` no schema (ADR-008); seed `admin@admin.com`/`admin123` na migration 8
- [ ] TM-02, Garantir hashes bcrypt compatíveis com `password_verify`

## Ordem Sugerida

1. T-01 → T-02 → T-03 → T-04 (serviço de autenticação)
2. T-05 (controller) e T-06/T-07 (evento de log)
3. Testes TT-01–TT-07; migração TM-01/TM-02 antes de testes de sucesso

## Lacunas Pendentes (🔴)

- 🔴 Schema real de `users` (colunas `email`/`password`) — ver unit `login`, Q1, e ADR-008.
- 🔴 Decidir se o log de falha deve incluir dados sensíveis (e-mail do tentante) e o ciclo de vida da pasta `logs/`.
