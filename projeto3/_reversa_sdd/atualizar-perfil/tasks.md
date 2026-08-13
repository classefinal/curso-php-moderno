# Atualizar Perfil (POST /usuario/perfil), Tarefas de Implementação

## Pré-requisitos

- [ ] Middleware `auth` funcional
- [ ] Tabela `users` com `password` (ver ADR-008)
- [ ] Unit `perfil` (GET) implementada (view + rota)

## Tarefas

- [ ] T-01, Registrar a rota `user_profile_update` (POST `/usuario/perfil`, controller `Users/Users`, `updateProfile`, middleware `auth`)
  - Origem no legado: `src/Configs/routes.php:155-166`
  - Critério de pronto: POST `/usuario/perfil` logado invoca `updateProfile`
  - Confiança: 🟢

- [ ] T-02, Implementar `validateUpdateUserPassword` (senha atual via `password_verify`, `new_password` obrigatório, confirmação igual, mínimo 8)
  - Origem no legado: `src/Services/Users/UsersService.php:37-60`
  - Critério de pronto: retorna mensagens específicas por falha
  - Confiança: 🟢

- [ ] T-03, Implementar `setUpdatedUserIntoSession` (remove hash, grava sessão, marca `profile_updated`)
  - Origem no legado: `src/Services/Users/UsersService.php:66-72`
  - Critério de pronto: sessão sem `password` + flag de flash
  - Confiança: 🟢

- [ ] T-04, Implementar `updateUserProfile` com UPDATE condicional (só nome vs nome+senha)
  - Origem no legado: `src/Services/Users/UsersService.php:79-142`
  - Critério de pronto: `UPDATE ... SET name` ou `SET name = ?, password = ?` conforme `new_password`
  - Confiança: 🟢

- [ ] T-05, Implementar `updateProfile` com redirect 302 no sucesso e 422 com view re-renderizada na falha
  - Origem no legado: `src/Controllers/Users/Users.php:43-61`
  - Critério de pronto: 302 `/usuario/perfil` ou 422 com `error`
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Só nome válido → 302 + `users.name` atualizado + sessão sem hash
- [ ] TT-02, Nome + senha válidos → 302 + hash bcrypt novo em `users.password`
- [ ] TT-03, Nome inválido (curto/longo/vazio) → 422 `'O nome deve ter entre 3 e 255 caracteres'`
- [ ] TT-04, Senha atual errada → 422 `'Senha atual incorreta'`
- [ ] TT-05, Confirmação diferente → 422 `'A confirmaçao de senha deve ser igual a nova senha'`
- [ ] TT-06, Senha curta → 422 `'A senha deve ter pelo menos 8 caracteres'`
- [ ] TT-07, Sem sessão → 303 `/logout` (middleware)
- [ ] TT-08, Flash `profile_updated` visível no GET seguinte e removido pós-resposta
- [ ] TT-09, `strip_tags` aplicado no nome

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Garantir coluna `users.password` (ADR-008) para `password_verify`/UPDATE
- [ ] TM-02, (Recomendado) alinhar cost de bcrypt do seed (16) com o padrão usado em `password_hash`

## Ordem Sugerida

1. T-02 → T-03 → T-04 (serviço) → T-01/T-05 (rota/controller)
2. Testes TT-01–TT-09 ao final

## Lacunas Pendentes (🔴)

- 🔴 Coluna `users.password` ausente na migration 8 (ADR-008) — toda a unit depende dela.
- 🟡 Cost de bcrypt inconsistente entre seed e atualização (TM-02).
