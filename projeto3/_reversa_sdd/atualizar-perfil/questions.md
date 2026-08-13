# Atualizar Perfil (POST /usuario/perfil), Perguntas e Lacunas

> Marcador 🔴 LACUNA — dependem de validação humana. Preencha abaixo e avise o Reversa.

## Q1. Coluna `users.password` 🔴

`updateUserProfile` depende de `users.password` (para `password_verify` e UPDATE), coluna ausente na migration 8 (ADR-008). Confirmar schema real e o impacto no fluxo de troca de senha.

## Q2. Cost de bcrypt inconsistente 🟡

O seed da migration 8 usa cost 16; `updateUserProfile` usa `password_hash($new, PASSWORD_BCRYPT)` com cost padrão. Confirmar se há política de cost para hashes de senha.

## Q3. Flash de sucesso e sessão 🟢 (confirmação)

Após sucesso, `setUpdatedUserIntoSession` remove o hash da sessão, mas o middleware `auth` re-popula a linha completa no próximo request. Confirmar que a sessão "sem hash" é intencional só no instante pós-update.

## Q4. Campos não repopulados no 422 🟡

No erro 422, a view re-renderiza com dados do banco, perdendo a digitação do usuário. Manter o comportamento legado ou repopular os campos do form?
