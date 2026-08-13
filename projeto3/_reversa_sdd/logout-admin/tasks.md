# Logout Admin (GET /admin/logout), Tarefas de Implementação

## Pré-requisitos

- [ ] Sessão PHP iniciada no bootstrap
- [ ] Response com `redirect` (Location + status customizado) disponível

## Tarefas

- [ ] T-01, Registrar a rota `admin_logout` (GET `/admin/logout`, controller `Admin/Login/AdminLogin`, `logoutAdminLogin`, sem middlewares)
  - Origem no legado: `src/Configs/routes.php:95-106`
  - Critério de pronto: GET `/admin/logout` invoca `logoutAdminLogin`
  - Confiança: 🟢

- [ ] T-02, Implementar `logoutAdminLogin` verificando `$_SESSION['user']` e delegando para `/logout` (303)
  - Origem no legado: `src/Controllers/Admin/Login/AdminLogin.php:61-67`
  - Critério de pronto: usuário comum logado → 303 `/logout`
  - Confiança: 🟢

- [ ] T-03, Implementar `unset($_SESSION['admin'])` e redirect 303 para `/`
  - Origem no legado: `src/Controllers/Admin/Login/AdminLogin.php:69-71`
  - Critério de pronto: admin logado → 303 `/` e sessão `admin` removida
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Admin logado faz GET `/admin/logout` → 303 `/` e `$_SESSION['admin']` ausente
- [ ] TT-02, Usuário comum faz GET `/admin/logout` → 303 `/logout` e `$_SESSION['user']` intacta até lá
- [ ] TT-03, Sem sessão faz GET `/admin/logout` → 303 `/` sem erro

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Nenhuma (sem persistência).

## Ordem Sugerida

1. T-01 → T-02 → T-03
2. Testes TT-01–TT-03 ao final

## Lacunas Pendentes (🔴)

- Nenhuma para esta unit. (Melhoria 🟡 de `session_regenerate_id` documentada no `design.md`.)
