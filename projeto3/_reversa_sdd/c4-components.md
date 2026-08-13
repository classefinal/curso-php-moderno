# C4 — Diagrama de Componentes (Nível 3)

> Gerado pelo **Arquiteto** em 2026-08-12. 🟢 CONFIRMADO
> Container foco: **Aplicação PHP** (único container com componentes internos relevantes).

```mermaid
flowchart TD
    subgraph Aplicação_PHP["Aplicação PHP (app.php)"]
        CORE["Core<br/>Environment, DB, Defer,<br/>Response, View, EventDispatcher"]
        ROUTER["Router + RouteResolver<br/>processRoutes / resolveRoute"]
        CONFIG["Configs<br/>routes.php (17), events.php (2)"]
        CTL["Controllers<br/>home, about, products,<br/>auth, users, cart, admin"]
        SVC["Services de negócio<br/>Products, Categories, Login,<br/>Users, Cart, Contact"]
        MW["Middlewares<br/>auth, preventLogged"]
        LIS["Listeners<br/>loginError, adminLoginError"]
        FUN["Functions<br/>path helpers, menu"]
        PAGES["Pages (7 views)"]
        COMP["Components (13 partials)"]
    end

    WS["Apache/mod_rewrite"] --> ROUTER
    ROUTER --> CONFIG
    ROUTER --> MW
    MW --> CTL
    CTL --> SVC
    SVC --> CORE
    CTL --> PAGES
    PAGES --> COMP
    CORE --> CONFIG
    CORE --> LIS
    LIS --> CORE
    FUN --> CORE
    CONFIG --> FUN

    CORE --> DB[(MySQL)]
    CORE --> FS[("logs/ (defer)")]
```

## Componentes e responsabilidades

| Componente | Arquivos | Responsabilidade |
|------------|----------|------------------|
| **Core** | `src/Services/{Environment,DB,Defer,Response,View,EventDispatcher}.php` | Infraestrutura: env, queries, buffer/response, dispatch de eventos, execução defer |
| **Router** | `src/Services/Router.php`, `RouteResolver.php` | Resolver rota por método + string/regex, encadear middlewares, carregar controllers |
| **Configs** | `src/Configs/routes.php`, `events.php` | Declaração declarativa de rotas e eventos |
| **Controllers** | `src/Controllers/*` (7) | Callbacks por rota: montar args, chamar services, responder/redirecionar |
| **Services negócio** | `src/Services/{Products,Categories,Login,Users,Cart,Contact}/` | Regras de negócio + queries |
| **Middlewares** | `src/Middlewares/auth.php`, `preventLogged.php` | Controle de acesso por sessão |
| **Listeners** | `src/Listeners/*.php` (2) | Reação a eventos de login recusado (defer de log) |
| **Functions** | `src/Functions/Functions.php` | Helpers de path e menu (isMenuAllowed, isMenuActive, getMenuItens) |
| **Pages** | `src/Pages/` (7) | Views server-rendered |
| **Components** | `src/Components/` (13) | Partials reutilizáveis (layout, navbar, product, auth, cart) |

## Fluxo de dependência típico

```
Request → Router → Middlewares → Controller → Service → Core(DB) → Core(View) → Response
                                                              ↓
                                              Events → Listeners → Defer (pós-flush)
```

## Acoplamentos observados

- **Controllers** dependem de Services e do Core (via `$configs`); não tocam o banco diretamente 🟢.
- **Services** dependem do Core (DB) e de `types.php` para shapes 🟢.
- **Views** dependem de Components e de dados tipados do controller 🟢.
- Sem inversão de dependências (procedural por decisão — ADR-001) — acoplamento implícito por funções globais e `$configs` 🟡.
