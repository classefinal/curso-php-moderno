# Autenticar (POST /login), Perguntas e Lacunas

> Marcador 🔴 LACUNA — dependem de validação humana. Preencha abaixo e avise o Reversa.

## Q1. Schema real de `users` (email/password) 🔴

O fluxo autentica com `WHERE email = ?` e `password_verify`, mas a migration 8 cria `users` **sem** `email` e `password` (ADR-008). Confirmar o schema real em produção (mesma pendência da unit `login`).

## Q2. Sessão guardando o hash de senha 🟡

`$_SESSION['user']` recebe a linha completa do `users`, incluindo o hash bcrypt. É aceitável manter (reimplementação fiel) ou deve-se guardar apenas `id`/campos seguros e recarregar do banco?

## Q3. Mensagem morta no retorno de sucesso 🟡

`loginAuthenticate` retorna `error = 'Um erro foi detectado'` junto com `success = true` (nunca exibida). Remover na reimplementação ou manter por fidelidade?

## Q4. Log de falhas e a pasta `logs/` 🟡

O listener cria `logs/` em runtime e appenda `{date}: {email}`. A pasta não existe no repositório. Confirmar se o log deve persistir (retenção, rotação) ou se o comportamento atual (append indefinido) é suficiente.
