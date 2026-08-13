# Legacy Impact — 001-correcoes-revisao

> Data: `2026-08-13`
> Feature: `001-correcoes-revisao` (Correções da Revisão P1–P14)
> Âncora: extração `/reversa` (`_reversa_sdd/architecture.md` + `domain.md`)
> Escala de severidade alinhada com `/reversa-audit`: CRITICAL, HIGH, MEDIUM, LOW.

## Arquivos afetados × componente × tipo × severidade

| Arquivo afetado | Componente (`architecture.md`) | Tipo | Severidade | Justificativa |
|-----------------|--------------------------------|------|------------|---------------|
| `src/Migrations/7_add_product_short_description.php` | Migrations | regra-alterada | HIGH | Corrige o DDL que impedia reprodução do schema em banco limpo (ADR-009). |
| `src/Migrations/8_create_users_table.php` | Migrations | regra-alterada | HIGH | `CREATE TABLE users` passa a declarar `email`/`password` que o próprio seed e o código usam (ADR-008); corrige regressão de segurança (hash gravado só no banco). |
| `src/Services/Login/LoginService.php` | Autenticação (`code-analysis.md#auth`) | regra-alterada | HIGH | Sessão deixa de carregar o hash de senha (exposição removida, P5) e sucesso retorna `error = null` (P13). |
| `src/Services/Users/UsersService.php` | Usuários/Perfil (`code-analysis.md#users`) | regra-alterada | MEDIUM | `password_hash` da atualização de perfil passa a usar `cost => 16`, uniformizando com seed e dummy (P6). |
| `src/Services/Cart/CartService.php` | Carrinho (`code-analysis.md#cart`) | regra-alterada | HIGH | Adiciona validação de estoque (P11), de existência/`active` antes do INSERT (P12), filtro `active` no JOIN do GET e detecção de falha/0 linhas no banco (P10). |
| `src/Controllers/Cart/Cart.php` | Carrinho (controller) | regra-alterada | MEDIUM | Flash de erro no caminho logado quando o serviço retorna `success: false`; fluxo de cookie segue silencioso. |
| `src/Controllers/NotFound.php` | Roteamento/NotFound (`code-analysis.md#products`) | regra-alterada | MEDIUM | Responde HTTP 404 na URI sem rota (P9). |
| `src/Pages/Cart/cart.php` | Views | regra-alterada | HIGH | Interpolações de dados de banco escapadas com `htmlspecialchars` (P7) e exibição do flash de erro do carrinho. |
| `src/Pages/Products/products.php`, `src/Pages/Products/product.php` (via componentes), `src/Pages/about.php` | Views | regra-alterada | HIGH | Escape nas interpolações (P7) — inclui os componentes `product_card.php`, `random_products_cards.php`, `categories_accordion_list.php`, `product_header.php`, `product_description.php`, `product_breadcrumb.php`. |
| `src/Pages/Login/login.php`, `src/Pages/Users/profile.php`, `src/Components/Login/login_form.php` | Views | regra-alterada | HIGH | Escape nas interpolações (P7). |
| `.gitignore` | Configuração de repositório | componente-novo | LOW | Pasta `logs/` (runtime) sai do versionamento (P14). |

## Diff conceitual por componente

### Migrations
- **Migration 7:** a instrução `ALTER TABLE products` deixou de usar `AFTER` de colunas criadas na mesma instrução (`short_description ... AFTER description_line` + `description_line ... AFTER short_description`), que era circular e quebrava em banco limpo. Agora as duas colunas são adicionadas sem `AFTER`. Nenhuma alteração semântica do schema final.
- **Migration 8:** `CREATE TABLE users` agora declara `email VARCHAR(255) NOT NULL UNIQUE` e `password VARCHAR(255) NOT NULL` antes do INSERT do seed — as colunas que o próprio seed e todo o fluxo de autenticação/perfil usam. Ambientes já migrados são indiferentes (runner sem checksum); bancos limpos passam a reproduzir o schema real.

### Autenticação (LoginService)
- **Sessão sem hash (P5):** `unset($user['password'])` é aplicado antes de gravar `$_SESSION['user']`/`$_SESSION['admin']`, alinhando com o padrão que o perfil já usava (`setUpdatedUserIntoSession`). O middleware `auth` continua recarregando o usuário por id quando precisa de dados frescos.
- **Sucesso com `error = null` (P13):** a string morta `'Um erro foi detectado'` foi removida dos dois retornos de sucesso.

### Usuários/Perfil (UsersService)
- `password_hash` da troca de senha passou a receber `['cost' => 16]`, idêntico ao seed e ao `DUMMY_PASSWORD_HASH`.

### Carrinho (CartService + controller + view)
- **Estoque (P11):** `addToCart` (banco) e `addToCartCookie`/`updateCartItemQuantityCookie` (cookie) consultam `products.stock` e bloqueiam quando a quantidade resultante excede o estoque. No cookie, a consulta `SELECT stock FROM products WHERE id = ? AND active = true` é feita por PK (custo desprezível).
- **Produto ativo/existente (P12):** antes do INSERT, o produto é validado com `active = true`; o JOIN do carrinho logado (`getCartItems`) passou a exigir `p.active = true`, e o enriquecimento do cookie já filtrava `active` (preservado).
- **Falha/0 linhas (P10):** UPDATE/DELETE/INSERT no banco retornam `success: false` quando `$result === false` (falha de statement) ou `$connection->affected_rows < 1` (0 linhas). O controller, apenas no caminho logado, grava `$_SESSION['flash']['error']` e segue com 302 (PRG preservado — ADR-003). Operações de cookie permanecem silenciosas.
- **View:** `cart.php` lê/limpa `$_SESSION['flash']`, exibe o flash de erro e escapa todas as interpolações.

### Roteamento (NotFound)
- `makeNotFound` agora chama `$configs['response'](404, $content)`, usando o mesmo mecanismo de status já empregado pelo login com 401. A view `not_found.php` é preservada.

### Views (escape P7)
- `about.php`, `products.php`, `login.php`, `profile.php`, `login_form.php` e os componentes de produto/categoria/produtos em destaque passam a escapar toda interpolação de dados do banco (e mensagens de flash) com `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. `profile.php` já escapava `name`/`email`; `cart.php` passou a escapar `image`, `name`, `description_line` e o flash.

### Configuração de repositório
- Novo `.gitignore` com `logs/` — a pasta de logs de runtime deixa de poluir o `git status`.

## Preservadas

Regras 🟢 do `_reversa_sdd/domain.md` que continuam intactas após a mudança:

| Regra | Fonte |
|-------|-------|
| Apenas produtos `active = true` aparecem no catálogo e apenas categorias ativas no accordion/filtro | `domain.md#Exibição-de-catálogo` |
| Produto só é exibido se ele e a categoria estiverem ativos | `domain.md#Exibição-de-catálogo` |
| Preço em centavos e exibição com `number_format($price/100, ...)`; total = `price * quantity` | `domain.md#Precificação` |
| Login de usuário exige `active = true AND admin = false`; admin exige `active = true AND admin = true` | `domain.md#Autenticação` |
| Senha mínima de 8 caracteres; email normalizado com `strtolower` | `domain.md#Autenticação` |
| Login recusado dispara evento e grava log em `logs/` após a resposta (defer) | `domain.md#Autenticação` |
| Usuário inexistente executa `password_verify` contra hash dummy (timing attack) | `domain.md#Autenticação` |
| Sessões separadas `$_SESSION['user']`/`$_SESSION['admin']`; `/logout` roteia admin | `domain.md#Autenticação` |
| Redirects pós-POST: 303 no login, demais 302 (PRG) | `domain.md#Autenticação` |
| Perfil: nome `strip_tags` 3–255; email não editável; troca exige senha atual, nova ≥ 8 e confirmação idêntica | `domain.md#Perfil` |
| Logado → carrinho em banco; visitante → cookie `cart_items` (30 dias, formato `id:qty`) | `domain.md#Carrinho` |
| Quantidade mínima 1; `decrease` com quantidade ≤ 1 remove o item | `domain.md#Carrinho` |
| Itens do cookie são enriquecidos apenas com produtos ativos | `domain.md#Carrinho` |
| Contato: campos obrigatórios, `FILTER_VALIDATE_EMAIL`, telefone no padrão `(00)0000-0000`, flash de feedback | `domain.md#Contato` |
| Decisões P3 (dashboard admin), P4 (seed mantido) e P8 (cookie sem assinatura) permanecem como documentadas nas specs, sem mudança de código | `_reversa_sdd/gaps.md#Resolvidos` |

## Modificadas

Regras 🟢 que foram alteradas ou removidas nesta feature:

| Regra | Fonte | O que mudou |
|-------|-------|-------------|
| Hash `PASSWORD_BCRYPT` com `cost 16` no seed/dummy | `domain.md#Autenticação` | Estendida à atualização de perfil — toda escrita de senha agora usa `cost => 16` |
| Sessão sobrescrita sem `password` após atualização de perfil | `domain.md#Perfil` | Estendida ao login: `$_SESSION['user']`/`$_SESSION['admin']` nunca contêm o hash |
| Operações do carrinho: adicionar (incrementa), aumentar, diminuir, remover | `domain.md#Carrinho` | Adicionado o vínculo com `products.stock` (bloqueio no add/increase) e `products.active` (bloqueio de inserção e filtro na leitura) |
| Feedback de carrinho: comportamento silencioso em todas as operações | `domain.md#Carrinho` (implícito) | Caminho logado (banco) passou a emitir flash de erro em falha/0 linhas; cookie permanece silencioso (P10 híbrido) |
| Página não encontrada respondia 200 (view `not_found.php`) | `_reversa_sdd/home/design.md` (fluxo alternativo) | Responde HTTP 404 (P9) |
| Migration 8 sem `email`/`password` no DDL (regressão documentada) | `domain.md#Seed-inicial` (🔴, ADR-008) | 🔴 resolvida: DDL agora reproduz o schema real (RN-01) |
| Migration 7 com `AFTER` cruzados (regressão documentada) | `_reversa_sdd/adrs/009-regressao-migration-7-after.md` | Corrigida: `ALTER TABLE` sem `AFTER` cruzados (RN-02) |
| `logs/` sem controle de versão | `_reversa_sdd/autenticar/requirements.md` (P14) | Novo `.gitignore` com `logs/` (RN-10) |
