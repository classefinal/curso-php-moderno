# Regression Watch — 001-correcoes-revisao

> Feature: `001-correcoes-revisao` (Correções da Revisão P1–P14)
> Data: `2026-08-13`
> Função: vigiar que as regras alteradas continuem verdadeiras nas próximas re-extrações `/reversa` e nas próximas features. Só o agente reverso (re-extração) move itens para "Arquivadas".

## Watch principal

| ID | Origem (arquivo, seção) | Regra esperada após mudança | Tipo de verificação | Sinal de violação |
|----|-------------------------|-----------------------------|---------------------|-------------------|
| W001 | `src/Migrations/8_create_users_table.php` (RN-01) | `CREATE TABLE users` declara `email` (UNIQUE, NOT NULL) e `password` (NOT NULL) antes do INSERT do seed | redação | Migration 8 sem `email`/`password`, ou INSERT do seed referenciando colunas inexistentes |
| W002 | `src/Migrations/7_add_product_short_description.php` (RN-02) | `ALTER TABLE products` adiciona as colunas **sem** `AFTER` de colunas criadas na mesma instrução | redação | `AFTER description_line`/`AFTER short_description` reaparecem cruzados na mesma instrução |
| W003 | `src/Services/Login/LoginService.php` (RN-03) | `$_SESSION['user']` e `$_SESSION['admin']` nunca contêm a coluna `password` após login | redação | `$_SESSION` gravada com o hash (`$user['password']`) sem `unset` |
| W004 | `src/Services/Login/LoginService.php` (RN-05) | Sucesso de `loginAuthenticate`/`adminLoginAuthenticate` retorna `error = null` | redação | String morta `'Um erro foi detectado'` reaparece no retorno de sucesso |
| W005 | `src/Services/Users/UsersService.php` (RN-05) | Toda escrita de senha usa `PASSWORD_BCRYPT` com `['cost' => 16]` | redação | `password_hash` do perfil sem `cost => 16` (cost divergente do seed/dummy) |
| W006 | `src/Services/Cart/CartService.php` (RN-06) | Adicionar ou `increase` respeita `products.stock` (banco e cookie); operação bloqueada se a quantidade resultante exceder o estoque | presença | Carinho aceita quantidade acima do estoque (banco ou cookie) |
| W007 | `src/Services/Cart/CartService.php` (RN-07) | Produto inexistente ou `active = false` não é adicionado (banco/cookie) e não aparece no GET do carrinho logado (JOIN com `p.active = true`) | presença | INSERT em `cart_items` com produto inativo/inexistente, ou JOIN do GET sem filtro `active` |
| W008 | `src/Services/Cart/CartService.php` + `src/Controllers/Cart/Cart.php` (RN-08) | Falha ou 0 linhas em INSERT/UPDATE/DELETE de carrinho no banco → `success: false` → flash de erro + 302; cookie permanece silencioso | presença | Operação de banco falha sem flash; ou cookie emitindo flash (silêncio quebrado) |
| W009 | `src/Controllers/NotFound.php` (RN-09) | URI sem rota responde HTTP 404 com a view `not_found.php` | presença | NotFound volta a responder 200 |
| W010 | Views (`cart.php`, `about.php`, `products.php` + componentes, `login.php`, `profile.php`, `login_form.php`) (RN-04) | Toda interpolação de dados do banco usa `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` na view | redação | Interpolação de dado do banco sem escape em qualquer dessas views/componentes |
| W011 | `.gitignore` (RN-10) | Pasta `logs/` (runtime) fora do versionamento | presença | `.gitignore` ausente ou sem `logs/`; `logs/` aparecendo em `git status` |

## Histórico de re-extrações

<!-- Preenchido pelo agente reverso ao rodar `/reversa` de novo: conferir cada W e registrar resultado. -->

## Arquivadas

<!-- Itens movidos aqui apenas por uma re-extração que confirmou a regra como estável. -->

## Observações

Itens que **nunca** foram regra 🟢 (inferidos/lacunas) e por isso não têm peso de regressão no watch principal:

- **Admin — escopo real (🔴/🟡):** o módulo admin contém somente autenticação; `/admin/dashboard` é rota planejada nunca implementada (decisão P3) — fora do escopo desta feature, sem código tocado.
- **Cookie do carrinho sem assinatura (P8):** decisão do usuário preservada; cookie é indicador de quantidade, validado na operação.
- **Seed mantido (P4):** `admin@admin.com` / `admin123` permanece na migration 8 — decisão consciente, não regressão.
