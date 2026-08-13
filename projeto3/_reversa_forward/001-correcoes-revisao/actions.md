<!--
Template de corpo do actions.md
Carregado por /reversa-to-do e atualizado por /reversa-coding.
-->

# Actions: Correções da Revisão (P1–P14)

> Identificador: `001-correcoes-revisao`
> Data: `2026-08-13`
> Roadmap: `_reversa_forward/001-correcoes-revisao/roadmap.md`

## Resumo

| Métrica | Valor |
|---------|-------|
| Total de ações | 14 |
| Paralelizáveis (`[//]`) | 7 |
| Maior cadeia de dependência | 5 (T006 → T007 → T008 → T010 → T011) |

> Fase 2 (Testes) omitida: o projeto não pratica TDD (sem framework/Composer). Validação é manual via `onboarding.md` e sintática via `php -l` (T014).

## Fase 1, Preparação

<!-- Migrations corrigidas in-place (D-01) e .gitignore criado (D-09). -->

| ID | Descrição | Dependências | Paralelismo | Arquivo alvo | Confidência | Status |
|----|-----------|--------------|-------------|--------------|-------------|--------|
| T001 | Remover as cláusulas `AFTER` cruzadas (`short_description ... AFTER description_line` e `description_line ... AFTER short_description`) da instrução `ALTER TABLE products` | - | `[//]` | `src/Migrations/7_add_product_short_description.php` | 🟢 | `[X]` |
| T002 | Adicionar ao `CREATE TABLE users` as colunas `email` (VARCHAR, UNIQUE, NOT NULL) e `password` (VARCHAR, NOT NULL), antes do INSERT do seed, alinhado ao `data-dictionary.md` | - | `[//]` | `src/Migrations/8_create_users_table.php` | 🟢 | `[X]` |
| T003 | Criar `.gitignore` na raiz com `logs/` | - | `[//]` | `.gitignore` | 🟢 | `[X]` |

## Fase 2, Testes

<!-- Omitida — ver Resumo. -->

## Fase 3, Núcleo

<!-- Lógica central das correções em serviços. -->

| ID | Descrição | Dependências | Paralelismo | Arquivo alvo | Confidência | Status |
|----|-----------|--------------|-------------|--------------|-------------|--------|
| T004 | Remover a coluna `password` do array antes de gravar `$_SESSION['user']`/`$_SESSION['admin']` (D-03) e, no sucesso, retornar `error = null` no lugar da string morta `'Um erro foi detectado'` (D-10), em `loginAuthenticate` e `adminLoginAuthenticate` | - | - | `src/Services/Login/LoginService.php` | 🟢 | `[X]` |
| T005 | Trocar `password_hash` da atualização de perfil para `PASSWORD_BCRYPT` com `['cost' => 16]`, uniformizando com seed e `DUMMY_PASSWORD_HASH` (D-05) | - | `[//]` | `src/Services/Users/UsersService.php` | 🟢 | `[X]` |
| T006 | Adicionar validação de estoque (D-06): ao adicionar (banco e cookie) e ao `increase`, consultar `products.stock` e bloquear a operação se a quantidade resultante exceder o estoque; em cookie, consultar `SELECT stock ... WHERE id = ? AND active = true` | - | - | `src/Services/Cart/CartService.php` | 🟢 | `[X]` |
| T007 | Adicionar validação de existência + `active = true` antes do INSERT (banco e cookie) e filtrar `active = true` no JOIN do carrinho logado no GET (D-07) | T006 | - | `src/Services/Cart/CartService.php` | 🟢 | `[X]` |
| T008 | Detectar falha/0 linhas nas operações de banco do carrinho (`affected_rows === 0` ou statement com erro) e retornar `success: false`; operações de cookie continuam silenciosas (D-08) | T007 | - | `src/Services/Cart/CartService.php` | 🟢 | `[X]` |

## Fase 4, Integração

<!-- Contrato HTTP: 404 global e flash no carrinho logado; escape nas views. -->

| ID | Descrição | Dependências | Paralelismo | Arquivo alvo | Confidência | Status |
|----|-----------|--------------|-------------|--------------|-------------|--------|
| T009 | Responder HTTP 404 via `$configs['response'](404, $content)` no carregamento da página não encontrada (D-02), preservando a view `not_found.php` | - | `[//]` | `src/Controllers/NotFound.php` | 🟢 | `[X]` |
| T010 | No `Cart.php` logado, checar o retorno `success: false` das funções de banco do `CartService` e, em falha, gravar flash de erro em `$_SESSION['flash']` antes do 302; fluxos de cookie permanecem sem flash (D-08, contrato `interfaces/carrinho-post.md`) | T008 | - | `src/Controllers/Cart/Cart.php` | 🟢 | `[X]` |
| T011 | Aplicar `htmlspecialchars(..., ENT_QUOTES)` nas interpolações de `src/Pages/Cart/cart.php` e exibir o flash de erro (D-04 + D-08) | T010 | - | `src/Pages/Cart/cart.php` | 🟢 | `[X]` |
| T012 | Aplicar `htmlspecialchars(..., ENT_QUOTES)` nas interpolações de `src/Pages/products.php` e `src/Pages/about.php` (D-04) | - | `[//]` | `src/Pages/products.php`, `src/Pages/about.php` | 🟢 | `[X]` |
| T013 | Aplicar `htmlspecialchars(..., ENT_QUOTES)` nas interpolações de `src/Pages/Login/login.php` e `src/Pages/Users/profile.php` (D-04) | - | `[//]` | `src/Pages/Login/login.php`, `src/Pages/Users/profile.php` | 🟢 | `[X]` |

## Fase 5, Polimento

<!-- Verificação sintática pós-correções. -->

| ID | Descrição | Dependências | Paralelismo | Arquivo alvo | Confidência | Status |
|----|-----------|--------------|-------------|--------------|-------------|--------|
| T014 | Rodar `php -l` em todos os arquivos PHP alterados (T001, T002, T004..T013) e confirmar sintaxe válida | T001, T002, T004, T005, T006, T007, T008, T009, T010, T011, T012, T013 | - | (verificação, nenhum arquivo alterado) | 🟢 | `[X]` |

## Notas de execução

- T012: o arquivo-alvo listado (`src/Pages/products.php`) corresponde na verdade a `src/Pages/Products/products.php` (caminho real do legado). As interpolações de dados do banco da página de produtos ficam nos componentes `product_card.php`, `random_products_cards.php`, `categories_accordion_list.php`, `product_header.php`, `product_description.php` e `product_breadcrumb.php` — todos escapados nesta ação para cumprir RF-06 (a view `products.php` apenas inclui componentes).
- T013: `login_form.php` (componente do formulário) também foi escapado (`$action`).
- T006: as funções de cookie `addToCartCookie`/`updateCartItemQuantityCookie` passaram a receber `mysqli $connection` para validar estoque/ativo — chamadas atualizadas no controller `Cart.php`.
- T008: falha/0 linhas detectado via `$result === false` (statement) e `$connection->affected_rows < 1` (0 linhas).

## Histórico de alterações

| Data | Alteração | Autor |
|------|-----------|-------|
| 2026-08-13 | Versão inicial gerada por `/reversa-to-do` | reversa |
