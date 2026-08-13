# ADR-005 — Eventos de login recusado com log pós-resposta (defer)

- **Status:** Aceito 🟢
- **Data:** 2026-08-12 (retroativo — commits `3af8f39` "wip: add dispatcher and logs", `6066f6a`, `a4c90f3` "wip: add event log")
- **Origem:** `src/Configs/events.php`, `src/Listeners/`, `src/Services/Defer.php`

## Contexto

Recusas de login precisavam deixar rastro sem atrasar a resposta ao usuário (que já espera o erro).

## Decisão

- Dispatcher de eventos (`createEventDispatcher`) com listeners inline ou em arquivo.
- Eventos `LoginRecused` e `AdminLoginRecused` disparam listeners que **agendam** (defer) a escrita do log.
- `response()`/`redirect()` executam os deferred **após** `flush` (`Connection: close`), liberando o cliente primeiro.
- Logs: `logs/YYYY-MM-DD-loginErrors.txt` e `logs/YYYY-MM-DD-adminLoginErrors.txt`, linha `{date}: {email}`.

## Consequências

- Usuário recebe a resposta antes do I/O de log terminar.
- Logs não existem no repositório (pasta `logs/` criada em runtime; fora do controle de versão 🟡).
- Monitoramento restrito a falhas de login — nenhum outro evento de negócio é logado.
