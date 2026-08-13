# Visão Geral Arquitetural — projeto3

> Gerado pelo **Arquiteto** em 2026-08-12.
> Escala: 🟢 CONFIRMADO | 🟡 INFERIDO | 🔴 LACUNA

## Visão resumida

**projeto3** é uma loja virtual didática em **PHP 8.5 procedural** (sem OOP, sem Composer). Front controller único (`public/index.php` → `app.php`) com router próprio, view por `extract()` + output buffering, banco **MySQL** via `mysqli`, sessões para autenticação e sistema próprio de migrations. Público: catálogo de produtos com categorias, carrinho (banco p/ logado, cookie p/ visitante), autenticação de usuário/admin e formulário de contato. **Não há checkout/finalização de compra.**

## Pilares

| Pilar | Decisão | Fonte |
|-------|---------|-------|
| **Paradigma** | Procedural global (funções + closures + arrays) | ADR-001 |
| **Sem dependências** | Bootstrap 5.3.8 + Font Awesome vendored; zero pacotes PHP | `dependencies.md`, ADR-001 |
| **Request flow** | `index.php` → `app.php` → `Router::processRoutes` → Controller → View → `Response` | `code-analysis.md` |
| **Banco** | MySQL/MariaDB + `mysqli`, prepared statements tipados `['type' => 's'|'i']` | ADR-004 |
| **Schema** | 7 tabelas finais + `migrations` de controle; 10 migrations | `data-dictionary.md` |
| **Moeda** | Preços em **centavos** (INT) | ADR-002 |
| **Estado de sessão** | `$_SESSION['user']` / `$_SESSION['admin']` + middlewares `auth`/`preventLogged` | ADR-007 |
| **Carrinho** | Logado → banco; visitante → cookie `cart_items` (30 dias) | ADR-006 |
| **Logging** | Eventos `LoginRecused`/`AdminLoginRecused` com escrita **defer pós-resposta** | ADR-005 |
| **Migrations** | Runner CLI `migrate.php` + tabela `migrations` | ADR-004 |

## Camadas (diretórios em `src/`)

```
public/            → index.php (front controller)
app.php            → bootstrap da aplicação (sessão, env, conexão, configs)
src/Configs/       → routes.php (17 rotas), events.php (2 eventos)
src/Controllers/   → 7 arquivos por domínio (callbacks de rota)
src/Services/      → infraestrutura (Router, RouteResolver, DB, View, Response, Defer,
                     EventDispatcher, Environment) + negócio (Products, Categories,
                     Login, Users, Cart, Contact)
src/Middlewares/   → auth.php, preventLogged.php
src/Listeners/     → handleLoginErrorEvent, handleAdminLoginErrorEvent
src/Functions/     → path helpers + menu (isMenuAllowed, getMenuItens, isMenuActive)
src/Pages/         → 7 views
src/Components/    → 13 partials reutilizáveis (layout, navbar, product, auth, cart)
src/Migrations/    → 10 migrations
migrate.php        → CLI de migrations
types.php          → anotações Psalm (shape dos dados)
.env               → credenciais do banco (não versionado? ver lacunas)
```

## Fluxo de uma requisição (resumo)

```
Browser → Apache/mod_rewrite → public/index.php → app.php
  ├─ Sessão (httponly), constantes de path (path.php), funções
  ├─ .env → Environment::loadEnv
  ├─ mysqli → DB::dbConnect
  ├─ defer + dispatcher + eventDispatcher
  └─ Router::processRoutes($configs)
        ├─ resolveRoute (GET/POST, string ou regex) — ordem importa
        ├─ middlewares encadeados (closures)
        └─ Controller call
              ├─ Services de negócio (queries tipadas)
              └─ view('Page', args) → output buffer → response()/redirect()
                    └─ flush → dispatcher roda ações defer (ex.: logs de login)
```

## Integrações externas

- **Nenhuma** API/webhook/evento externo 🟢.
- Único consumo externo: **iframe do Google Maps** na página Sobre (embutido, sem API key) 🟢.
- Saída de rede apenas por HTTP/HTML (server-rendered); sem AJAX/fetch 🟡.

## Topologia de deployment

- Servidor Apache + PHP 8.5 + MySQL. Sem Dockerfile/docker-compose/cloud (doc_level completo → sem `deployment.md`).

## Dívidas técnicas (síntese)

1. 🔴 Migrations 7 e 8 não reproduzem o schema real (ADR-008/009).
2. 🔴 Rota `/admin/dashboard` inexistente (ADR-010).
3. 🟡 XSS: views imprimem dados sem escape (`<?= $var ?>`); só o nome de perfil escapa.
4. 🟡 `dbPrepareAndExecute` sem tratamento de erro de prepared statement.
5. 🟡 Duplicação: `$productId` calculado 2x; lógica de carrinho banco/cookie duplicada; sem testes automatizados.
6. 🟡 Sessão sem `samesite`/`secure`; `DUMMY_PASSWORD_HASH` em `const` pública.
7. 🟡 Sem checkout/pedidos/estoque-validado (quantidade do carrinho não respeita `stock`).

## Resumo de componentes e containers

Detalhamento nos artefatos C4: `c4-context.md` (Nível 1), `c4-containers.md` (Nível 2), `c4-components.md` (Nível 3), `erd-complete.md` (modelo de dados).
