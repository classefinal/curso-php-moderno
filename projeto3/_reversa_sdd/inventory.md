# Inventário — projeto3

> Gerado pelo **Scout** em 2026-08-12.
> Nível de confiança: 🟢 **CONFIRMADO** — extraído diretamente do código-fonte.

## Visão geral

**projeto3** é um framework PHP **procedural** (sem OOP) construído como projeto educacional. Simula uma loja virtual com catálogo público de produtos, autenticação de usuários e área administrativa. Não possui dependências externas de PHP (sem Composer): é PHP 8.1+ puro com assets vendored (Bootstrap 5 e Font Awesome).

## Estrutura de pastas

```
projeto3/
├── app.php                 # Bootstrap da aplicação (front controller)
├── path.php                # Helpers de caminho (constants das pastas em src/)
├── migrate.php             # CLI de migrações (php migrate.php)
├── types.php               # Tipos Psalm (annotations de dados)
├── agents.md               # Documentação de contexto para agentes
├── .env.example            # Configuração de banco (MySQL)
├── public/
│   ├── index.php           # Entry point HTTP → require app.php
│   ├── .htaccess           # Rewrite de URLs (front controller Apache)
│   ├── assets/             # Vendor: bootstrap/, fontawesome/, images/
│   └── php_errors.log      # Log de erros de runtime
├── src/
│   ├── Components/         # 13 partials reutilizáveis (layout, produto, auth)
│   ├── Configs/
│   │   ├── routes.php      # 17 rotas (GET/POST)
│   │   └── events.php      # 2 eventos
│   ├── Controllers/        # 8 controllers (funções make*/do*/view*/send*)
│   ├── Functions/          # 1 arquivo de funções utilitárias
│   ├── Listeners/          # 2 listeners (Login, AdminLogin)
│   ├── Middlewares/        # 2 middlewares (auth, preventLogged)
│   ├── Migrations/         # 10 migrações SQL
│   ├── Pages/              # 8 templates de página
│   └── Services/           # 14 serviços (infra + negócio)
└── doc/                    # Documentação do framework (14 arquivos)
```

## Tecnologias

| Item | Detalhe |
|------|---------|
| Linguagem | PHP 8.1+ (`declare(strict_types=1)` em todos os arquivos) |
| Paradigma | Procedural — funções globais, closures e arrays associativos |
| Front-end | Bootstrap 5.3.8 + Font Awesome 7 (vendored em `public/assets/`, CDN-less) |
| Banco de dados | MySQL/MariaDB via mysqli (camada `src/Services/DB.php`) |
| Gerenciador de pacotes | Nenhum (sem `composer.json`) |
| Type system | Psalm annotations documentadas em `types.php` |

## Entry points

| Caminho | Tipo | Descrição |
|---------|------|-----------|
| `public/index.php` | HTTP front controller | Requer `app.php`; `.htaccess` reescreve todas as URLs não resolvidas para aqui |
| `app.php` | Bootstrap | Sessão, output buffering, carrega config, cria dependências, processa rotas |
| `migrate.php` | CLI | Executa migrações pendentes (`php migrate.php`) |

## Rotas (17 em `src/Configs/routes.php`)

| ID | URL | Método | Controller/call | Middleware |
|----|-----|--------|-----------------|------------|
| home | `/` | GET | Home::makeHome | — |
| about | `/sobre` | GET | About::makeAbout | — |
| about_send | `/sobre` | POST | About::sendContact | — |
| products | `/produtos` | GET | Products::makeProducts | — |
| product | `/produtos/{slug}` (regex) | GET | Products::makeProduct | — |
| admin_login_page | `/admin/login` | GET | AdminLogin::makeAdminLogin | preventLogged |
| admin_login | `/admin/login` | POST | AdminLogin::validateAdminLogin | preventLogged |
| admin_logout | `/admin/logout` | GET | AdminLogin::logoutAdminLogin | — |
| login_page | `/login` | GET | Login::makeLogin | preventLogged |
| login | `/login` | POST | Login::validateLogin | preventLogged |
| logout | `/logout` | GET | Login::logoutLogin | — |
| user_profile | `/usuario/perfil` | GET | Users::viewProfile | auth |
| user_profile_update | `/usuario/perfil` | POST | Users::updateProfile | auth |
| cart_page | `/carrinho` | GET | Cart::makeCart | — |
| cart_add | `/carrinho/adicionar` | POST | Cart::doAddToCart | — |
| cart_update | `/carrinho/atualizar` | POST | Cart::doUpdateCartQuantity | — |
| cart_remove | `/carrinho/remover` | POST | Cart::doRemoveCartItem | — |

## Banco de dados (superficial — Data Master fará a análise detalhada)

- Camada de acesso: `src/Services/DB.php` (`dbConnect`, `dbPrepareAndExecute`, `dbExecuteStm`, `dbClose`).
- Configuração via `.env` (`DB_SERVER`, `DB_PORT`, `DB_DATABASE`, `DB_USER`, `DB_PASSWORD`).
- 10 migrações em `src/Migrations/`: tabelas `migrations`, `categories`, `products`, `users`, `carts`, `cart_items`, `contacts` (mais criação/remoção de tabela `test`).

## Testes

- **Nenhum framework de teste** identificado.
- Nenhum arquivo `*.test.*` / `*.spec.*` / BDD no código da aplicação.

## CI/CD, Docker, package managers

- **Nenhum**: sem `.github/workflows`, `Jenkinsfile`, `.gitlab-ci.yml`, `Dockerfile`, `docker-compose.yml` ou `composer.json`.

## Eventos (em `src/Configs/events.php`)

- `AdminLoginRecused` → `AdminLogin/AdminLoginErrorListener::handleAdminLoginErrorEvent`
- `LoginRecused` → `Login/LoginErrorListener::handleLoginErrorEvent`
