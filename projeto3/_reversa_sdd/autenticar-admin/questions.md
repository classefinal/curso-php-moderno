# Autenticar Admin (POST /admin/login), Perguntas e Lacunas

> Marcador 🔴 LACUNA — dependem de validação humana. Preencha abaixo e avise o Reversa.

## Q1. Destino pós-login admin 🔴

`validateAdminLogin` redireciona para `/admin/dashboard`, rota inexistente (ADR-010). Confirmar destino pretendido após login admin bem-sucedido.

## Q2. Schema de `users` (email/password) 🔴

Mesma pendência da unit `login`/`autenticar` (ADR-008): a migration 8 não cria `email`/`password`, mas a autenticação admin depende delas. Confirmar schema real.

## Q3. Seed do admin 🔴

A migration 8 insere `Administrador` / `admin@admin.com` / `admin123` (bcrypt cost 16) com `admin=true`. Manter credencial fixa em seed ou gerar a primeira senha em setup interativo?

## Q4. Sessão admin com hash de senha 🟡

`$_SESSION['admin']` guarda a linha completa do `users`, incluindo o hash bcrypt. Manter por fidelidade ou guardar campos mínimos?
