# Home (GET /), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| GET | `/` | — (query params ignorados) | HTML da página inicial | 200 |
| GET | URI vazia | — | HTML da página inicial (rota default do Router) | 200 |

Parâmetros da view (`makeHome`): `title`, `routes`.

## Fluxo Principal

1. Requisição chega em `public/index.php` → `app.php` (bootstrap: sessão, constantes de path, `.env`, conexão mysqli, `defer`/dispatcher, configs). `src/Services/Router.php`
2. `processRoutes` normaliza a URI: se vazia, usa a rota default `home` sem passar por `resolveRoute`. `src/Services/Router.php`
3. `requireController('Home')` carrega `src/Controllers/Home.php`; `makeHome($configs, $route, $uri)` é invocado. `src/Controllers/Home.php`
4. `makeHome` monta `title` e `routes = getMenuItens(...)` e chama `$configs['view']('home', $args)`. `src/Controllers/Home.php`
5. `createView` faz `extract($args)` + `require_once` de `src/Pages/home.php` com output buffering, retornando o HTML. `src/Services/View.php`
6. `$configs['response'](content: $content)` → HTTP 200, `Connection: close`, flush, dispara ações defer. `src/Services/Response.php`

## Fluxos Alternativos

- **URI não vazia mas sem rota correspondente:** `resolveRoute` retorna a rota do controller `NotFound` (`makeNotFound`). Na reimplementação, responde **HTTP 404** (P9 — o legado respondia 200). `src/Services/RouteResolver.php`
- **Método não GET:** o `RouteResolver` ignora rotas cujo método não casa (a rota `home` só tem `GET`). `src/Services/RouteResolver.php`

## Dependências

- **Router** (`processRoutes`), para resolução da rota default.
- **RouteResolver** (`resolveRoute`), para o caso geral de URI.
- **View** (`createView`), para `extract()` + buffer.
- **Response** (`response`), para envio do HTML e flush + defer.
- **Functions** (`getMenuItens`, `getMenuItens` usa `isMenuAllowed`/`isMenuActive`), para o menu.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Rota default embutida no Router quando URI vazia | `src/Services/Router.php` | 🟢 |
| View por `extract()` + output buffering | `src/Services/View.php` | 🟢 |
| Menu derivado da própria lista de rotas (`inMenu`/`order`) | `src/Functions/Functions.php` | 🟢 |

## Estado Interno

- Nenhum estado persistente. Apenas uso transitório de sessão para flash (não utilizado nesta página).

## Observabilidade

- Nenhum log específico desta página. Falhas de rota caem no controller `NotFound`. `src/Controllers/NotFound.php`

## Riscos e Lacunas

- 🟢 Nenhuma lacuna conhecida: página estática simples.
- 🟡 Query string é ignorada — URLs como `/?foo=bar` renderizam a mesma página.
