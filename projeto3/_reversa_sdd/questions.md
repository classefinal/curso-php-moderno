# Perguntas para Validação — projeto3

> Gerado pelo Revisor em 2026-08-12
> Atualizado em 2026-08-13 — **todas as 14 perguntas respondidas** e aplicadas às specs.

---

## Pergunta 1 — ✅ Respondida

**Contexto:** Migration `8_create_users_table.php` — `CREATE TABLE users` cria apenas `id, name, active, admin, created_at, updated_at` (sem `email`/`password`), mas o `INSERT` logo abaixo referencia `email` e `password` (ADR-008).
**Spec afetada:** [`_reversa_sdd/login/requirements.md`], [`_reversa_sdd/autenticar/requirements.md`], [`_reversa_sdd/login-admin/requirements.md`], [`_reversa_sdd/autenticar-admin/requirements.md`], [`_reversa_sdd/perfil/requirements.md`]
**Pergunta:** Qual é o schema real da tabela `users` em produção? A migration 8 como está **falha** no `INSERT` (colunas inexistentes). Existe uma correção manual aplicada no banco, ou a migration precisa ser corrigida na spec (adicionar `email` e `password` ao CREATE TABLE)?
**Impacto:** Se a migration for corrigida, as specs passam a assumir `users.email`/`users.password` como 🟢 CONFIRMADO. Se o banco foi corrigido manualmente, o legado tem uma divergência entre código e DB que precisa ser documentada.

**Resposta:** O banco tem email/password. A migration 8 deve ser corrigida para incluir as colunas `email` e `password` no CREATE TABLE. `users.email`/`users.password` viram 🟢 CONFIRMADO nas specs de auth/perfil.

---

## Pergunta 2 — ✅ Respondida

**Contexto:** Migration `7_add_product_short_description.php` — `ALTER TABLE products ADD short_description ... AFTER description_line, ADD description_line ... AFTER short_description` (ADR-009).
**Spec afetada:** [`_reversa_sdd/produtos/requirements.md`], [`_reversa_sdd/produto/requirements.md`]
**Pergunta:** Em banco limpo, o `ALTER` falha porque as colunas `short_description`/`description_line` não existem no momento do `AFTER` cruzado. Essa migration já rodou com sucesso em algum ambiente, ou a coluna `description_line` veio de outro caminho? A spec deve documentar a regressão como bug conhecido?
**Impacto:** Determina se `products.description_line` é uma coluna real assumida pelos `SELECT` de produtos/carrinho ou um risco de schema.

**Resposta:** Precisa corrigir: ambas as colunas são usadas. O `AFTER` pode ser removido da instrução (as duas colunas são reais e devem existir).

---

## Pergunta 3 — ✅ Respondida

**Contexto:** `/admin/dashboard` — usado como destino de redirect pós-login admin (`AdminLogin.php:40`) e no middleware `preventLogged.php:18`, mas **não existe rota** com esse path no `routes.php` (ADR-010).
**Spec afetada:** [`_reversa_sdd/login-admin/requirements.md`], [`_reversa_sdd/autenticar-admin/requirements.md`]
**Pergunta:** Para onde o admin deve ir após o login? A rota `/admin/dashboard` não existe (cai no NotFound). Existe um dashboard planejado, ou o admin deve ser redirecionado para outra página (ex.: `/`, `/sobre`, `/carrinho`)?
**Impacto:** Define o redirect de sucesso das unidades de admin e o comportamento do `preventLogged`.

**Resposta:** Manter a rota `/admin/dashboard` — a página será criada posteriormente. O redirect atual permanece como destino de sucesso; a rota fica documentada como planejada.

---

## Pergunta 4 — ✅ Respondida

**Contexto:** Migration `8_create_users_table.php:23-41` — seed cria `Administrador` / `admin@admin.com` / senha `admin123` (hash bcrypt cost 16), hardcoded na migration.
**Spec afetada:** [`_reversa_sdd/autenticar-admin/requirements.md`]
**Pergunta:** Manter o seed padrão com credenciais conhecidas `admin@admin.com`/`admin123` na reimplementação? É um risco de segurança conhecido; existe gestão de credenciais de admin (ex.: exigir troca no primeiro acesso)?
**Impacto:** Mantém ou remove a regra de seed nas specs de autenticação admin.

**Resposta:** Manter o seed (decisão aceita; documentar nas specs que as credenciais padrão existem).

---

## Pergunta 5 — ✅ Respondida

**Contexto:** `LoginService.php:91` e `:151` — `$_SESSION['user']`/`$_SESSION['admin']` recebem a **linha completa** do `users`, incluindo o hash bcrypt da senha.
**Spec afetada:** [`_reversa_sdd/autenticar/requirements.md`], [`_reversa_sdd/autenticar-admin/requirements.md`]
**Pergunta:** A sessão deve continuar guardando o hash de senha (reimplementação fiel) ou deve guardar apenas `id` e campos seguros, recarregando do banco quando necessário?
**Impacto:** Reduz ou mantém o risco de exposição do hash se a sessão vazar (estado 🔴/🟡 nas specs de auth).

**Resposta:** Remover o hash dos dados de sessão. A sessão deve guardar apenas campos seguros (sem `password`).

---

## Pergunta 6 — ✅ Respondida

**Contexto:** `LoginService.php:7` (`DUMMY_PASSWORD_HASH` cost 16) e `8_create_users_table.php:25` (seed cost 16). O cost 16 é mais alto que o padrão PHP (10-12), deixando `password_verify` mais lento.
**Spec afetada:** [`_reversa_sdd/autenticar/requirements.md`], [`_reversa_sdd/autenticar-admin/requirements.md`]
**Pergunta:** Manter cost 16 ou normalizar para o padrão? (Nota: `password_verify` usa o cost embutido no hash — não é quebra funcional, apenas performance.)
**Impacto:** Documenta a decisão de segurança nas specs de auth.

**Resposta:** Manter cost 16 em todos os lugares (seed, dummy e reimplementação).

---

## Pergunta 7 — ✅ Respondida

**Contexto:** Views renderizam dados do banco **sem** `htmlspecialchars`: `cart.php:50-86` (nome/imagem/descrição do produto), `products.php:27` (descrição da categoria), `about.php:15,18,22`, `login.php:16,18` e `profile.php:23` (flash/título). Apenas `profile.php:33,37` escapam (`htmlspecialchars`).
**Spec afetada:** [`_reversa_sdd/produtos/requirements.md`], [`_reversa_sdd/carrinho/requirements.md`], [`_reversa_sdd/sobre/requirements.md`], [`_reversa_sdd/login/requirements.md`], [`_reversa_sdd/perfil/requirements.md`]
**Pergunta:** Manter a saída sem escape (fidelidade ao legado) ou adicionar `htmlspecialchars` na reimplementação? Há alguma fonte confiável de dados (admin) capaz de injetar conteúdo no catálogo hoje?
**Impacto:** Marca as views como risco XSS (🟡) ou seguro (🟢) nas specs.

**Resposta:** Corrigir — todas as interpolações de dados nas views devem usar `htmlspecialchars` na reimplementação.

---

## Pergunta 8 — ✅ Respondida

**Contexto:** `CartService.php:209-239, 241-250` — cookie `cart_items` em texto puro `id:qtd,id:qtd`, sem assinatura/HMAC; cliente pode forjar quantidade.
**Spec afetada:** [`_reversa_sdd/carrinho-adicionar/requirements.md`], [`_reversa_sdd/carrinho/requirements.md`]
**Pergunta:** O carrinho de visitante precisa de integridade (assinatura) ou o formato simples é aceitável? (O preço vem do banco via JOIN, então a adulteração só afeta quantidade.)
**Impacto:** Adiciona ou não requisito de segurança ao contrato do cookie nas specs de carrinho.

**Resposta:** O cookie indica apenas a quantidade de itens selecionados para compra — formato simples aceito, sem assinatura.

---

## Pergunta 9 — ✅ Respondida

**Contexto:** `NotFound.php:16-24` — `makeNotFound` chama `$configs['response'](content: $content)` sem status → página 404 retorna **HTTP 200**.
**Spec afetada:** [`_reversa_sdd/home/design.md`], [`_reversa_sdd/sobre/design.md`] (fluxos alternativos citam NotFound)
**Pergunta:** Manter 200 para página não encontrada (comportamento atual) ou retornar 404 correto? Não existe unit dedicada ao NotFound no `_reversa_sdd`.
**Impacto:** Corrige o contrato HTTP do roteamento e possivelmente cria uma unit `not-found`.

**Resposta:** Mudar para HTTP 404.

---

## Pergunta 10 — ✅ Respondida

**Contexto:** `CartService.php:275-298, 300-315` (funções de cookie) e `53-151` (funções de banco).
**Spec afetada:** [`_reversa_sdd/carrinho-atualizar/requirements.md`], [`_reversa_sdd/carrinho-remover/requirements.md`], [`_reversa_sdd/carrinho-adicionar/requirements.md`]
**Pergunta (detalhada):** O que acontece hoje em 3 cenários, todos **sem avisar o usuário**:

1. **Cookie — remover/atualizar item que não existe:** `removeCartItemCookie` e `updateCartItemQuantityCookie` retornam `['success' => true]` mesmo quando o `product_id` não está no cookie. Na prática, o cliente clica "+" ou no "remover" e **nada muda**, mas o sistema age como se tivesse funcionado (o redirect acontece e a página recarrega igual).

2. **Banco — INSERT falha (ex.: banco caiu):** `addToCart` chama `dbPrepareAndExecute` sem conferir o retorno. Se o INSERT/UPDATE falhar, a função ainda responde `success => true` e redireciona para `/carrinho` como se o item tivesse sido adicionado — o carrinho simplesmente não muda.

3. **Banco — UPDATE/DELETE sem efeito:** `updateCartItemQuantity`/`removeCartItem` usam `WHERE product_id = ?` sem checar se o item existe; quando não existe, o `dbPrepareAndExecute` roda e afeta 0 linhas, mas o retorno é `success => true` do mesmo jeito.

**Pergunta:** Devo documentar nas specs que a reimplementação deve **tratar esses 3 cenários com flash de erro** (avisar o usuário quando a ação não teve efeito) ou **manter o comportamento silencioso** (fidelidade ao legado, apenas reclassificar como 🟡 documentado)?
**Impacto:** Decide se as units de carrinho ganham caminhos de erro/feedback ou mantêm o contrato atual de "sucesso sempre".

**Resposta:** **Híbrido** — feedback para falhas de **banco**, silêncio para **cookie**:
- **Banco (logado):** falha ou 0 linhas no INSERT/UPDATE/DELETE → flash de erro e 302 `/carrinho` (não fingir sucesso).
- **Cookie (visitante):** permanece silencioso — o cookie é apenas indicador de quantidade; item ausente apenas regrava o cookie inalterado.
- Aplicado como RF/regra nas units `carrinho-adicionar` (RF-08), `carrinho-atualizar` (RF-07) e `carrinho-remover` (RF-05), com fluxos alternativos nos design.md correspondentes.

---

## Pergunta 11 — ✅ Respondida

**Contexto:** `CartService.php:53-89, 91-151` — `addToCart` e `updateCartItemQuantity` **não verificam `stock`**; o cliente pode adicionar quantidade maior que o estoque (unidades `carrinho-adicionar`, `carrinho-atualizar`).
**Spec afetada:** [`_reversa_sdd/carrinho-adicionar/requirements.md`], [`_reversa_sdd/carrinho-atualizar/requirements.md`]
**Pergunta:** O controle de estoque é intencionalmente ausente ou deve ser validado na adição/atualização de quantidade?
**Impacto:** Adiciona ou não regra de negócio de estoque às specs de carrinho.

**Resposta:** Precisa verificar estoque — adicionar regra de validação de `stock` na adição e na atualização de quantidade.

---

## Pergunta 12 — ✅ Respondida

**Contexto:** `CartService.php:79-89` — logado: `INSERT INTO cart_items (cart_id, product_id, ...)` sem verificar se o `product_id` existe/está ativo; produto inexistente viola FK `products(id)` sem tratamento; produto inativo entra no carrinho (só é filtrado no enrich do GET para visitante).
**Spec afetada:** [`_reversa_sdd/carrinho-adicionar/requirements.md`], [`_reversa_sdd/carrinho/requirements.md`]
**Pergunta:** Validar existência + `active` do produto antes de adicionar ao carrinho, ou manter o comportamento atual?
**Impacto:** Define o contrato de validação da adição no carrinho logado.

**Resposta:** Verificar se o produto está ativo antes de adicionar; se não estiver ativo, **não deve nem ser exibido no carrinho** (nem para logado, nem para visitante).

---

## Pergunta 13 — ✅ Respondida

**Contexto:** `LoginService.php:93-96, 153-156` — retorno de sucesso inclui `error = 'Um erro foi detectado'` (string morta, nunca exibida porque `success=true`).
**Spec afetada:** [`_reversa_sdd/autenticar/requirements.md`], [`_reversa_sdd/autenticar-admin/requirements.md`]
**Pergunta:** Remover a string morta na reimplementação ou manter por fidelidade?
**Impacto:** Limpa ou preserva um quirk do retorno do serviço de login.

**Resposta:** Remover a string morta — no sucesso o `error` deve ser `null`.

---

## Pergunta 14 — ✅ Respondida

**Contexto:** `src/Listeners/Login/LoginErrorListener.php` e `AdminLoginErrorListener.php` — criam a pasta `logs/` em runtime e fazem append indefinido de `{data}: {email}` em `logs/YYYY-MM-DD-*Errors.txt`. A pasta `logs/` não existe no repositório.
**Spec afetada:** [`_reversa_sdd/autenticar/requirements.md`], [`_reversa_sdd/autenticar-admin/requirements.md`]
**Pergunta:** Manter o log com append indefinido, adicionar retenção/rotação, ou descartar? A pasta deve ser versionada (ex.: `.gitkeep`)?
**Impacto:** Define o contrato de auditoria das falhas de login nas specs.

**Resposta:** Não versionar a pasta `logs/` — manter o append em runtime, sem retenção adicional.
