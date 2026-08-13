# Perfil (GET /usuario/perfil), Perguntas e Lacunas

> Marcador 🔴 LACUNA — dependem de validação humana. Preencha abaixo e avise o Reversa.

## Q1. Coluna `users.email` 🔴

View e middleware dependem de `users.email`, coluna ausente na migration 8 (ADR-008). Confirmar schema real e o comportamento sem a coluna (o `htmlspecialchars($user['email'])` quebraria).

## Q2. E-mail não editável 🟡

O campo e-mail é `disabled` no formulário. Confirmar se e-mail será imutável por design ou se haverá fluxo de alteração futura (a unit `atualizar-perfil` só altera `name` e `password`).

## Q3. Recarga do usuário por request 🟢 (confirmação)

O middleware `auth` recarrega o usuário do banco a cada acesso, mantendo a sessão como referência apenas de `id`/`active`. Confirmar se essa recarga é intencional (evita dados stale) — comportamento já inferido como 🟢.

## Q4. Hash de senha no `$configs['user']` 🟡

`getUserById` retorna `SELECT *`, então a linha completa (com hash bcrypt) trafega até a view. Manter por fidelidade ou selecionar colunas mínimas no perfil?
