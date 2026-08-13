# ADR-003 — Redirect 303 após POST

- **Status:** Aceito 🟢
- **Data:** 2026-08-12 (retroativo — commit `618579b` "wip: changed redirect to 303", 2026-05-01)
- **Origem:** `git show 618579b`

## Contexto

Redirecionamentos após requisições POST (login, perfil, carrinho, contato) usavam 302. Refrescar a página podia reenviar o POST (duplicação de contato/inserções) dependendo do navegador.

## Decisão

- Redirecionamentos **pós-POST** passam a usar **303 See Other** (`Response::redirect(..., 303)`).
- Redirecionamentos de navegação/middleware seguem com **302**.
- Commit `618579b` aplicou a mudança em `routes.php`, `Users.php`, `UsersService.php` e `types.php`.

## Consequências

- Refreshes após POST não reenviam o formulário (PRG correto).
- Exceção: logouts e flushes de usuário/admin usam 303 também; navegação comum permanece 302.
