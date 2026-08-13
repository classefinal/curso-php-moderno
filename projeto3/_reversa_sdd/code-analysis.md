# Análise de Código — projeto3

> Gerado pelo **Arqueólogo** em 2026-08-12.
> Nível de documentação: **completo**.
> Escala de confiança: 🟢 CONFIRMADO | 🟡 INFERIDO | 🔴 LACUNA

## Visão geral

Framework web **procedural** em PHP puro (sem OOP, sem Composer), estilo arquitetura de pastas por camada (`Controllers`, `Services`, `Pages`, `Components`, `Listeners`, `Middlewares`, `Configs`, `Migrations`). Simula uma loja virtual: catálogo de produtos, autenticação (usuário e admin), perfil e carrinho de compras.

### Requisitos de runtime

- **PHP 8.5+** — o código usa operador pipe `|>` e a função `array_last()`, confirmados como válidos apenas nesta versão. `php -l` e execução prática validaram ambos. 🟢
- MySQL/MariaDB com extensão `mysqli`.
- Apache com `mod_rewrite` (front controller em `public/`).

### Fluxo de requisição

1. `public/index.php` → `app.php` (front controller).
2. `app.php`: inicia sessão (`httponly`), output buffering, define constantes de caminho via `path.php`, carrega funções e serviços, lê `routes.php` e `events.php`, carrega `.env`, cria `defer`/`dispatcher`, conecta ao banco, monta `$configs`, cria event dispatcher e chama `processRoutes($configs)`.
3. `processRoutes` (Router.php): normaliza a URI, resolve a rota por método HTTP + string/regex, carrega o controller e executa a cadeia de middlewares, terminando na função `call` da rota.
4. O controller monta a view (via closure `view` com `extract()` + output buffering) e responde com `response()` ou `redirect()` — ambos disparam o `dispatcher` de ações adiadas (defer) após flush.

## Infraestrutura (Serviços)

### Router (`src/Services/Router.php`) 🟢
- `processRoutes`: URI vazia → rota default `home`; senão `parse_url` + `rtrim('/')` e `resolveRoute`.
- Middleware chain: closures aninhadas, `array_pop` da pilha; o último chama o callback final (`$route['call']`).
- Rota não encontrada → controller `NotFound` (`makeNotFound`, HTTP 200).
- `requireController`/`requireMiddleware` carregam arquivos via constantes `CONTROLLERS`/`MIDDLEWARES`.

### RouteResolver (`src/Services/RouteResolver.php`) 🟢
- `resolveRoute`: itera rotas na ordem declarada; ignora rotas sem `value`/`controller`/`methods`; casa método HTTP (`$_SERVER['REQUEST_METHOD']`); igualdade de string ou `preg_match` para `isRegex`. Retorna a **primeira** rota compatível (ordem importa — ex.: `/login` GET e POST são rotas distintas).

### DB (`src/Services/DB.php`) 🟢
- `dbConnect`: lê credenciais de env vars, `mysqli_connect`, `die()` em falha, charset `utf8mb4`.
- `dbPrepareAndExecute`: prepared statements com formato `['type' => 's'|'i', 'value' => $val]`. **Sem tratamento de erro** em `mysqli_prepare`/`execute` (retorna `false` em falha, os callers chamam `mysqli_num_rows` direto). 🔴
- `dbExecuteStm`: `mysqli_query` para DDL (migrações).

### Environment (`src/Services/Environment.php`) 🟢
- `loadEnv`: parse simples de `.env` (`CHAVE=valor`), ignora comentários e linhas vazias, trim de aspas, `putenv` + `$_ENV`. Não sobrescreve vars já definidas no ambiente.

### Defer/Dispatcher (`src/Services/Defer.php`) 🟢
- `createDefer`: lista de closures; `defer()` agenda, `dispatcher()` executa todas (padrão de execução pós-resposta).

### Response (`src/Services/Response.php`) 🟢
- `response(int $code=200, ?string $content)`: captura buffer, headers `Connection: close` + `Content-length`, `http_response_code`, echo, `flush`, dispara deferred.
- `redirect(string $to, int $code=302)`: `ob_clean()`, `Location`, `flush`, dispara deferred. Códigos usados: 302 (default) e 303.

### View (`src/Services/View.php`) 🟢
- `createView`: closure que faz `extract($args)` e `require_once` da página (`src/Pages/{path}.php`) com output buffering, retornando o HTML.

### EventDispatcher (`src/Services/EventDispatcher.php`) 🟢
- `createEventDispatcher`: closure que, para um evento, executa listeners (closure inline ou função de arquivo em `src/Listeners/`). Registra em `$configs['eventDispatcher']`.

### Configs
- `routes.php`: 17 rotas; cada rota: `id`, `value` (string ou regex), `controller`, `call`, `isRegex`, `inMenu`, `label`, `order`, `allowedRoutes`, `methods`, `middlewares`. 🟢
- `events.php`: `AdminLoginRecused` → `handleAdminLoginErrorEvent`; `LoginRecused` → `handleLoginErrorEvent`. 🟢

### Functions (`src/Functions/Functions.php`) 🟢
- Menu do navbar: `isMenuAllowed` (esconde login quando logado; exige `inMenu`+`label`+`value`), `isMenuActive` (URI atual ou `allowedRoutes` contém rota atual — ex.: `products` marca ativo na rota `product`), `getMenuItens` (filtra + ordena por `order`).

## Módulos de negócio

### home
- `makeHome` renderiza `home.php` (estático) com título e menu. Rota default quando a URI está vazia. Complexidade baixa.

### about
- GET `/sobre`: `makeAbout` exibe flash de sucesso/erro e o formulário (nome, email, telefone + iframe do Google Maps).
- POST `/sobre`: `sendContact` valida (obrigatórios, `FILTER_VALIDATE_EMAIL`, regex de telefone `^\(\d{2}\)\d{4,5}-\d{4}$`), normaliza para `+55<dígitos>`, insere em `contacts` e redireciona 302 com flash. 🟢

### products
- GET `/produtos`: `makeProducts` — lista produtos ativos paginada (`?limit=` 5–30, default 10; `?page=`; `?categoryId=`), categorias ativas ordenadas por nome, categoria ativa selecionada, 6 produtos aleatórios em destaque (`ORDER BY RAND()`).
- GET `/produtos/{id}` (regex `^\/produtos\/[a-zA-Z0-9]+$`): `makeProduct` — busca por ID extraído do último segmento da URI; produto inexistente → `response(404, 'not found')`.
- 🟡 **Observação:** `getProductById` (ProductsService.php:123-139) calcula `$productId` **duas vezes** com lógica idêntica (primeira versão tradicional + segunda com operador pipe `|>`). A primeira atribuição é redundante.

### auth (usuário)
- GET `/login` e POST `/login` (middleware `preventLogged`): valida email/senha, autentica usuário `active=true AND admin=false`, grava `$_SESSION['user']`.
- `validateLoginInfo`: obrigatórios + email válido + senha ≥ 8 chars.
- Login recusado → evento `LoginRecused` → listener agenda (defer) escrita em `logs/{data}-loginErrors.txt`.
- GET `/logout`: se `$_SESSION['admin']` existir, redireciona para `/admin/logout`; senão `unset($_SESSION['user'])` e vai para `/`.
- Timing attack mitigado com `password_verify` contra hash dummy quando o usuário não existe. 🟢
- **Middlewares:**
  - `auth` (rotas de perfil): exige `$_SESSION['user']['id']` e `active`; recarrega o usuário do banco (`getUserById`) e injeta em `$configs['user']`; senão redireciona `/logout` (303).
  - `preventLogged` (rotas de login): redireciona admin para `/admin/dashboard` (302) e usuário para `/usuario/perfil` (302) se já logado.

### users
- GET/POST `/usuario/perfil` (middleware `auth`): `viewProfile` / `updateProfile`.
- `updateUserProfile`: nome `strip_tags` 3–255 chars; alteração de senha exige senha atual correta, nova ≥ 8 e confirmação igual; hash `PASSWORD_BCRYPT`; `UPDATE users SET name=?, password=?`; session atualizada sem a coluna password + flag `profile_updated` (limpa pós-resposta via defer).
- Falha → HTTP 422 com a view; sucesso → 302.

### cart
- GET `/carrinho`: se logado, carrega `carts`/`cart_items`; senão lê cookie `cart_items` e enriquece com dados de produtos ativos.
- POST `/carrinho/adicionar`: `product_id` (int ≥ 1); logado → banco; visitante → cookie.
- POST `/carrinho/atualizar`: `action` ∈ {increase, decrease}; decrease remove quando quantity ≤ 1.
- POST `/carrinho/remover`: remove item (banco ou cookie).
- `calculateCartTotal`: soma `price * quantity` em centavos (exibição `number_format($total/100, 2, ',', '.')`).
- Cookie: `cart_items` = `id:qtd,id:qtd`, 30 dias. 🟢

### admin
- GET/POST `/admin/login` (middleware `preventLogged`), GET `/admin/logout`.
- `adminLoginAuthenticate`: usuário `active=true AND admin=true`; falha → evento `AdminLoginRecused` → log em `logs/{data}-adminLoginErrors.txt`.
- Seed na migration 8: `admin@admin.com` / `admin123` (bcrypt cost 16).
- 🔴 **LACUNA:** após login de admin, o sistema redireciona para `/admin/dashboard` (AdminLogin.php:40, preventLogged.php:18, navbar.php:37-43), mas **não existe rota nem controller para `/admin/dashboard`** em `routes.php`. O redirect cai no NotFound.

## Dicionário de dados — resumo

Entidades: `migrations`, `categories`, `products`, `users`, `carts`, `cart_items`, `contacts`. Detalhamento completo em `data-dictionary.md`.

🔴 **LACUNA no schema:** a migration `8_create_users_table.php` cria `users` **sem** as colunas `email` e `password`, mas o próprio INSERT da migration e todo o código de login/perfil usam essas colunas. O schema real do banco deve incluí-las; as migrations não reproduzem o schema atual do banco.

## Alertas e riscos identificados

| # | Severidade | Descrição | Local |
|---|-----------|-----------|-------|
| 1 | 🔴 | Migration 8 cria `users` sem `email`/`password` (INSERT referencia colunas inexistentes) | `src/Migrations/8_create_users_table.php:11-41` |
| 2 | 🔴 | Migration 7 usa `AFTER description_line` e `AFTER short_description` no mesmo ALTER — colunas ainda não existem no momento, ALTER provavelmente falha em banco limpo | `src/Migrations/7_add_product_short_description.php:11` |
| 3 | 🔴 | `/admin/dashboard` referenciado (redirects + navbar) mas sem rota registrada | `routes.php`, `AdminLogin.php:40`, `preventLogged.php:18` |
| 4 | 🟡 | `dbPrepareAndExecute` sem tratamento de erro de prepared statement | `src/Services/DB.php:44-62` |
| 5 | 🟡 | `getProductById` calcula `$productId` duas vezes (código duplicado) | `src/Services/Products/ProductsService.php:123-139` |
| 6 | 🟡 | XSS: views imprimem dados do banco com `<?= $var ?>` sem escapar (ex.: `product['name']`, `$item['name']`); apenas o nome do perfil usa `htmlspecialchars` | `src/Components/Product/product_header.php`, `src/Pages/Cart/cart.php` |
| 7 | 🟡 | `getActiveCategoryById` não filtra `active=true` e retorna `[]` em vez de `null` quando não encontra | `src/Services/Categories/CategoriesService.php:30-48` |
| 8 | 🟡 | Retorno de sucesso do login traz `'error' => 'Um erro foi detectado'` (mensagem enganosa, não exibida) | `src/Services/Login/LoginService.php:95,155` |
| 9 | 🟡 | Cookie de sessão sem `samesite`/`secure` (apenas `httponly`) | `app.php:5-7` |
| 10 | 🟡 | Route regex de produto aceita letras (`[a-zA-Z0-9]+`) mas é tratado como inteiro — URIs como `/produtos/abc` não retornam 404 (vão ao NotFound do produto? não: o filtro int retorna null → 404) | `routes.php:64`, `ProductsService.php:132-141` |

## Entidades e confiança

Entidades identificadas: `Contact`, `Product`, `Category`, `User`, `Cart`, `CartItem`, `Migration` (runner), `Route` (config). Todos com schema 🟢 CONFIRMADO por migrations, exceto as colunas `email`/`password` de `users` (🔴, ver alerta 1).
