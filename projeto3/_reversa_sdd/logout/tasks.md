# Logout (GET /logout), Tarefas de Implementação

## Pré-requisitos

- [ ] Sessão PHP iniciada no bootstrap
- [ ] Response com `redirect` (Location + status customizado) disponível

## Tarefas

- [ ] T-01, Registrar a rota `logout` (GET `/logout`, controller `Login/Login`, `logoutLogin`, sem middlewares)
  - Origem no legado: `src/Configs/routes.php:131-142`
  - Critério de pronto: GET `/logout` invoca `logoutLogin`
  - Confiança: 🟢

- [ ] T-02, Implementar `logoutLogin` verificando `$_SESSION['admin']` e delegando para `/admin/logout` (303)
  - Origem no legado: `src/Controllers/Login/Login.php:62-68`
  - Critério de pronto: admin logado → 303 `/admin/logout`
  - Confiança: 🟢

- [ ] T-03, Implementar `unset($_SESSION['user'])` e redirect 303 para `/`
  - Origem no legado: `src/Controllers/Login/Login.php:70-72`
  - Critério de pronto: usuário logado → 303 `/` e sessão `user` removida
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Usuário logado faz GET `/logout` → 303 `/` e `$_SESSION['user']` ausente
- [ ] TT-02, Admin logado faz GET `/logout` → 303 `/admin/logout` e `$_SESSION['admin']` intacta até lá
- [ ] TT-03, Sem sessão faz GET `/logout` → 303 `/` sem erro

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Nenhuma (sem persistência).

## Ordem Sugerida

1. T-01 → T-02 → T-03
2. Testes TT-01–TT-03 ao final

## Lacunas Pendentes (🔴)

- Nenhuma para esta unit. (Melhoria 🟡 de `session_regenerate_id` documentada no `design.md`.)
