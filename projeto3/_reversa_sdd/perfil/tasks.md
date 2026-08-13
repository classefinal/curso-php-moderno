# Perfil (GET /usuario/perfil), Tarefas de Implementação

## Pré-requisitos

- [ ] Middleware `auth` funcional (recarga por `getUserById`)
- [ ] Tabela `users` com `email` (ver ADR-008)
- [ ] `defer`/dispatcher pós-resposta disponível

## Tarefas

- [ ] T-01, Registrar a rota `user_profile` (GET `/usuario/perfil`, controller `Users/Users`, `viewProfile`, middleware `auth`)
  - Origem no legado: `src/Configs/routes.php:143-154`
  - Critério de pronto: GET `/usuario/perfil` logado invoca `viewProfile`
  - Confiança: 🟢

- [ ] T-02, Implementar `authMiddleware` validando `$_SESSION['user']['id']`/`active`, recarregando via `getUserById` e injetando `$configs['user']`
  - Origem no legado: `src/Middlewares/auth.php`
  - Critério de pronto: sem sessão/inativo → 303 `/logout`; válido → `$configs['user']` definido
  - Confiança: 🟢

- [ ] T-03, Implementar `getUserById` com `SELECT * FROM users WHERE id = ? AND active = true LIMIT 1`
  - Origem no legado: `src/Services/Users/UsersService.php:13-31`
  - Critério de pronto: retorna `null` para inexistente/inativo
  - Confiança: 🟢

- [ ] T-04, Implementar `viewProfile` montando a view `Users/profile` com `title`, `user` e `routes`, agendando o `unset` de `profile_updated` via `defer`
  - Origem no legado: `src/Controllers/Users/Users.php:18-35`
  - Critério de pronto: view renderiza e flash é limpo pós-resposta
  - Confiança: 🟢

- [ ] T-05, Implementar a view `src/Pages/Users/profile.php` com alertas (erro + flash), form POST `/usuario/perfil` com nome (escaped, required), e-mail (disabled, escaped) e campos `old_password`, `new_password`, `password_confirmation`
  - Origem no legado: `src/Pages/Users/profile.php`
  - Critério de pronto: HTML com os campos corretos e escapes aplicados
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Usuário logado: GET `/usuario/perfil` → 200 com form preenchido
- [ ] TT-02, Sem sessão: → 303 `/logout`
- [ ] TT-03, Usuário inativo/inexistente: → 303 `/logout`
- [ ] TT-04, Flash: com `profile_updated` → alert verde; após resposta flag removida
- [ ] TT-05, E-mail renderizado como disabled

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Garantir coluna `users.email` (ADR-008) para renderização do campo e-mail

## Ordem Sugerida

1. T-03 (serviço) → T-02 (middleware) → T-01 (rota)
2. T-04 (controller) → T-05 (view)
3. Testes TT-01–TT-05 ao final

## Lacunas Pendentes (🔴)

- 🔴 Coluna `users.email` ausente na migration 8 (ADR-008) — necessária para esta view.
- 🔴 Confirmar se o e-mail deve permanecer não editável (campo disabled) ou se haverá fluxo de alteração de e-mail.
