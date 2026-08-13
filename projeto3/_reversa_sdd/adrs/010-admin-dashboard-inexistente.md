# ADR-010 — Rota /admin/dashboard referenciada mas nunca registrada

- **Status:** Aceito (intenção não implementada) 🔴
- **Data:** 2026-08-12 (retroativo — presente desde `55fad5e` "feat: add admin-login flow", 2026-03)
- **Origem:** `git log -S "dashboard" -- src/Configs/routes.php` (nenhuma ocorrência), `AdminLogin.php`, `preventLogged.php`, `navbar.php`

## Contexto

Desde a criação do fluxo de login de admin, o sistema aponta para `/admin/dashboard` como destino pós-login:
- `AdminLogin::adminLoginAuthenticate` → `redirect('/admin/dashboard', 302)`
- `preventLogged` (admin logado) → `redirect('/admin/dashboard', 302)`
- Navbar (`isMenuAllowed`/menu) referencia o painel admin.

Nenhuma rota `/admin/dashboard` jamais foi registrada em `routes.php` (verificado via `git log -S`).

## Decisão (observada)

O painel administrativo foi **planejado mas nunca implementado**; os redirecionamentos permanecem apontando para uma rota morta.

## Consequências

- 🔴 Admin logado cai no **NotFound** (HTTP 200, view de erro) ao tentar acessar o painel.
- A percepção de "área administrativa" do projeto é ilusória — só há autenticação de admin.
- Correção sugerida: implementar `/admin/dashboard` ou redirecionar para `/produtos`/uma rota existente.

## Resolução (2026-08-13 — validação com o usuário, P3)

- Decisão: **manter** a rota `/admin/dashboard` como destino de sucesso do login admin; a **página será criada posteriormente** (rota planejada, não implementada).
- Specs atualizadas: `login-admin` e `autenticar-admin` tratam o redirect como 🟢 com a página marcada como pendente.
