# Login Admin (GET /admin/login), Tarefas de Implementação

## Pré-requisitos

- [ ] View `Login/login` e component `login_form` disponíveis
- [ ] Middleware `preventLogged` funcional
- [ ] Pipeline de middlewares do Router operacional

## Tarefas

- [ ] T-01, Registrar a rota `admin_login_page` (GET `/admin/login`, controller `Admin/Login/AdminLogin`, `makeAdminLogin`, middleware `preventLogged`)
  - Origem no legado: `src/Configs/routes.php:72-82`
  - Critério de pronto: GET `/admin/login` sem sessão renderiza a página
  - Confiança: 🟢

- [ ] T-02, Implementar `makeAdminLogin` reutilizando a view `Login/login` com `title = 'Login administrativo'`, `routes` e `action = '/admin/login'`
  - Origem no legado: `src/Controllers/Admin/Login/AdminLogin.php:18-27`
  - Critério de pronto: formulário aponta para POST `/admin/login` com título correto
  - Confiança: 🟢

- [ ] T-03, Garantir que `preventLogged` bloqueia admin e usuário na rota admin
  - Origem no legado: `src/Middlewares/preventLogged.php` (reuso)
  - Critério de pronto: sessões ativas redirecionam antes de renderizar
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, GET `/admin/login` sem sessão retorna 200 com formulário `action=/admin/login`
- [ ] TT-02, GET `/admin/login` com `$_SESSION['admin']` redireciona `/admin/dashboard` (302)
- [ ] TT-03, GET `/admin/login` com `$_SESSION['user']` redireciona `/usuario/perfil` (302)

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Nenhuma específica (depende de `users` para o POST — unit `autenticar-admin`, ADR-008)

## Ordem Sugerida

1. T-01 → T-02 → T-03 (reuso de middleware já existente)
2. Testes TT-01–TT-03 ao final

## Lacunas Pendentes (🔴)

- 🔴 Destino `/admin/dashboard` inexistente no `routes.php` (ADR-010) — mesmo impacto da unit `login`.
- 🔴 Depende das colunas `users.email`/`users.password` para o fluxo POST (ADR-008) — registrada em `autenticar-admin`.
