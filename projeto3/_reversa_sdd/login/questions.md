# Login (GET /login), Perguntas e Lacunas

> Marcador 🔴 LACUNA — dependem de validação humana. Preencha abaixo e avise o Reversa.

## Q1. Schema real da tabela `users` 🔴

A migration `8_create_users_table.php` cria `users` **sem** as colunas `email` e `password`, mas todo o código de login (e o próprio INSERT da migration) depende delas. Qual é o schema real em produção?

- Sugestão (ADR-008): `users(id, name, email VARCHAR(255) UNIQUE, password VARCHAR(255), active BOOLEAN, admin BOOLEAN, created_at, updated_at)`
- A confirmação permite que o fluxo POST `/login` (unit `autenticar`) seja reimplementado com fidelidade.

## Q2. Redirect de admin em `/login` 🔴

O middleware `preventLogged` redireciona admin logado para `/admin/dashboard`, rota que **não existe** no `routes.php` (ADR-010). Manter o comportamento atual (cai em 404), criar a rota ou mudar o destino?

## Q3. Exposição do `DUMMY_PASSWORD_HASH` 🟡

A constante `DUMMY_PASSWORD_HASH` é um hash bcrypt real de senha conhecida (cost 16), pública no `LoginService.php`. Mitigação de timing attack mantida, mas recomenda-se gerar hash próprio por instalação. Manter como está ou rotular como segredo interno?
