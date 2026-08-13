# ADR-007 — Autenticação: bcrypt + hash dummy anti-timing + sessões separadas

- **Status:** Aceito 🟢
- **Data:** 2026-08-12 (retroativo — commits `55fad5e`, `52c6e34`, `289d674` "wip: fixed auth")
- **Origem:** `src/Services/Login/LoginService.php`, `src/Middlewares/auth.php`, `src/Middlewares/preventLogged.php`

## Contexto

Dois tipos de conta (usuário comum e admin) coexistindo na mesma tabela `users`, com fluxos de login distintos. Precisava de segurança mínima em hash e tempo de resposta.

## Decisão

- Hash `PASSWORD_BCRYPT` (seed de admin com `cost 16`).
- Usuário inexistente executa `password_verify` contra `DUMMY_PASSWORD_HASH` (const no `LoginService`) para uniformizar tempo de resposta — mitigação de timing attack.
- Sessões separadas: `$_SESSION['user']` (login comum, `admin=false`) e `$_SESSION['admin']` (login admin, `admin=true`).
- Middleware `preventLogged` impede login duplo; `auth` valida perfil recarregando o usuário do banco.
- Email normalizado com `strtolower` antes da consulta.

## Consequências

- Tempo de resposta uniforme para usuário inexistente vs senha errada.
- Separação total entre as sessões (admin nunca loga como usuário no mesmo fluxo).
- `DUMMY_PASSWORD_HASH` é hash real de senha conhecida (custo 16) — sensível a brute-force se exposto; é `const` pública 🟡.
- 🔴 Seed/regressão da migration 8 compromete a criação do admin (ADR-008).
