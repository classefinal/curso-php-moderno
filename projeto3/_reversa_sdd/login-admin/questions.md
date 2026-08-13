# Login Admin (GET /admin/login), Perguntas e Lacunas

> Marcador 🔴 LACUNA — dependem de validação humana. Preencha abaixo e avise o Reversa.

## Q1. Destino `/admin/dashboard` 🔴

O middleware `preventLogged` e o sucesso de `validateAdminLogin` redirecionam para `/admin/dashboard`, rota que **não existe** no `routes.php` (ADR-010). Criar a rota, redirecionar para outra página ou manter o comportamento (404)?

## Q2. Schema de `users` para autenticação admin 🔴

O fluxo admin usa `WHERE email = ? AND active = true AND admin = true` e `password_verify`, mas a migration 8 não cria `email`/`password` (ADR-008). Confirmar o schema real em produção (mesma pendência da unit `login`).

## Q3. Redirecionamento de usuário comum em `/admin/login` 🟡

`preventLogged` envia usuário comum logado para `/usuario/perfil`. Manter na reimplementação?

## Q4. Página de destino pós-login admin 🟡

Mesmo com o dashboard inexistente, o fluxo de sucesso retorna `redirect('/admin/dashboard', 302)`. Confirmar o destino pretendido para reimplementar com fidelidade.
