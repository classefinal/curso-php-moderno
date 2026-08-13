# Login (GET /login), Tarefas de Implementação

## Pré-requisitos

- [ ] Pipeline de middlewares do Router funcional (`executeMiddlewares` com `$next`)
- [ ] `preventLogged` registrado e aplicável a rotas
- [ ] View (`createView`) e Response disponíveis

## Tarefas

- [ ] T-01, Registrar a rota `login_page` (GET `/login`, controller `Login/Login`, `makeLogin`, `inMenu`, middleware `preventLogged`)
  - Origem no legado: `src/Configs/routes.php:108-118`
  - Critério de pronto: GET `/login` sem sessão renderiza a página
  - Confiança: 🟢

- [ ] T-02, Implementar `makeLogin` chamando a view `Login/login` com `title`, `routes` e `action = '/login'`
  - Origem no legado: `src/Controllers/Login/Login.php:18-27`
  - Critério de pronto: view recebe os 3 parâmetros e responde 200
  - Confiança: 🟢

- [ ] T-03, Implementar `preventLoggedMiddleware` redirecionando admin (`/admin/dashboard`, 302) e usuário (`/usuario/perfil`, 302) quando a sessão existe, e chamando `$next()` caso contrário
  - Origem no legado: `src/Middlewares/preventLogged.php`
  - Critério de pronto: login duplo impedido nos dois papéis
  - Confiança: 🟢

- [ ] T-04, Implementar a view `src/Pages/Login/login.php` com h1, alerta de erro opcional e o `login_form`
  - Origem no legado: `src/Pages/Login/login.php`
  - Critério de pronto: HTML renderiza título e form
  - Confiança: 🟢

- [ ] T-05, Implementar `login_form` com `action` dinâmica, método POST, `autocomplete="off"`, campos `email` (type email, required, autofocus) e `password` (type password, required), botão "Entrar"
  - Origem no legado: `src/Components/Login/login_form.php`
  - Critério de pronto: form válido com os 2 campos e botão
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, GET `/login` sem sessão retorna 200 com formulário
- [ ] TT-02, GET `/login` com `$_SESSION['user']` redireciona para `/usuario/perfil` (302)
- [ ] TT-03, GET `/login` com `$_SESSION['admin']` redireciona para `/admin/dashboard` (302)

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Nenhuma específica para a rota GET (depende de `users` para o fluxo POST — ver unidade `autenticar` e ADR-008)

## Ordem Sugerida

1. T-03 (middleware) em paralelo com T-01 (rota)
2. T-02 (controller) → T-04/T-05 (views)
3. Testes TT-01–TT-03 ao final

## Lacunas Pendentes (🔴)

- 🔴 O destino `/admin/dashboard` do middleware não é uma rota registrada (ADR-010) — decidir entre criar a rota ou mudar o redirect.
- 🔴 O fluxo de login como um todo depende das colunas `users.email`/`users.password` ausentes na migration 8 (ADR-008) — pendência de schema, registrada na unit `autenticar`.
