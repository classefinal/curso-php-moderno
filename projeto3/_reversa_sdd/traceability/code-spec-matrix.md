# Code/Spec Matrix — projeto3

> Mapeia cada arquivo do legado para a unit de spec correspondente.
> Legenda: 🟢 coberto · 🟡 coberto parcialmente/com lacuna · n/a infraestrutura ou candidato a análise adicional.

## Controllers

| Arquivo do legado | Unit correspondente | Cobertura |
|-------------------|---------------------|-----------|
| `src/Controllers/Home.php` | `home/` | 🟢 |
| `src/Controllers/About.php` | `sobre/`, `enviar-contato/` | 🟢 |
| `src/Controllers/NotFound.php` | n/a (404 global do router) | 🟢 (P9: deve responder 404) |
| `src/Controllers/Products/Products.php` | `produtos/`, `produto/` | 🟢 |
| `src/Controllers/Login/Login.php` | `login/`, `autenticar/`, `logout/` | 🟢 |
| `src/Controllers/Admin/Login/AdminLogin.php` | `login-admin/`, `autenticar-admin/`, `logout-admin/` | 🟢 |
| `src/Controllers/Users/Users.php` | `perfil/`, `atualizar-perfil/` | 🟢 |
| `src/Controllers/Cart/Cart.php` | `carrinho/`, `carrinho-adicionar/`, `carrinho-atualizar/`, `carrinho-remover/` | 🟢 |

## Services

| Arquivo do legado | Unit correspondente | Cobertura |
|-------------------|---------------------|-----------|
| `src/Services/Login/LoginService.php` | `autenticar/`, `autenticar-admin/` | 🟢 |
| `src/Services/Users/UsersService.php` | `perfil/`, `atualizar-perfil/` | 🟢 |
| `src/Services/Cart/CartService.php` | `carrinho/`, `carrinho-adicionar/`, `carrinho-atualizar/`, `carrinho-remover/` | 🟢 |
| `src/Services/Products/ProductsService.php` | `produtos/`, `produto/` | 🟢 |
| `src/Services/Products/RandomProductsService.php` | `produtos/`, `produto/` | 🟢 |
| `src/Services/Categories/CategoriesService.php` | `produtos/` | 🟢 |
| `src/Services/View.php` | n/a (sistema de views `extract` + ob_start) | n/a |
| `src/Services/Router.php` | n/a (infra de roteamento) | n/a |
| `src/Services/RouteResolver.php` | n/a (infra de resolução) | n/a |
| `src/Services/Response.php` | n/a (infra de resposta/redirect) | n/a |
| `src/Services/DB.php` | n/a (infra de banco) | n/a |
| `src/Services/Defer.php` | n/a (execução adiada) | n/a |
| `src/Services/EventDispatcher.php` | n/a (infra de eventos) | n/a |
| `src/Services/Environment.php` | n/a (infra de configuração) | n/a |
| `src/Functions/Functions.php` | n/a (helpers: `getMenuItens`, `getRequirePath`) | n/a |

## Listeners e Middlewares

| Arquivo do legado | Unit correspondente | Cobertura |
|-------------------|---------------------|-----------|
| `src/Listeners/Login/LoginErrorListener.php` | `autenticar/` | 🟢 |
| `src/Listeners/AdminLogin/AdminLoginErrorListener.php` | `autenticar-admin/` | 🟢 |
| `src/Middlewares/preventLogged.php` | `login/`, `login-admin/`, `autenticar/`, `autenticar-admin/` | 🟢 |
| `src/Middlewares/auth.php` | `perfil/`, `atualizar-perfil/` | 🟢 |

## Configs

| Arquivo do legado | Unit correspondente | Cobertura |
|-------------------|---------------------|-----------|
| `src/Configs/routes.php` | todas as units (roteamento; 17 entradas, 14 paths) | 🟢 |
| `src/Configs/events.php` | `autenticar/`, `autenticar-admin/` | 🟢 |

## Pages (views)

| Arquivo do legado | Unit correspondente | Cobertura |
|-------------------|---------------------|-----------|
| `src/Pages/home.php` | `home/` | 🟢 |
| `src/Pages/about.php` | `sobre/` | 🟢 |
| `src/Pages/Products/products.php` | `produtos/` | 🟢 |
| `src/Pages/Products/product.php` | `produto/` | 🟢 |
| `src/Pages/Login/login.php` | `login/`, `login-admin/` | 🟢 |
| `src/Pages/Users/profile.php` | `perfil/`, `atualizar-perfil/` | 🟢 |
| `src/Pages/Cart/cart.php` | `carrinho/` | 🟢 |
| `src/Pages/not_found.php` | n/a (404 global) | 🟢 (P9: view do 404) |

## Components

| Arquivo do legado | Unit correspondente | Cobertura |
|-------------------|---------------------|-----------|
| `src/Components/header.php` | todas (layout) | 🟢 |
| `src/Components/footer.php` | todas (layout) | 🟢 |
| `src/Components/navbar.php` | todas (menu, itens de `inMenu`) | 🟢 |
| `src/Components/Empty/empty.php` | `produtos/`, `home/` (estado vazio) | 🟡 |
| `src/Components/Login/login_form.php` | `login/`, `login-admin/` | 🟢 |
| `src/Components/Products/product_card.php` | `produtos/` | 🟢 |
| `src/Components/Products/products_list.php` | `produtos/` | 🟢 |
| `src/Components/Products/categories_accordion_list.php` | `produtos/` | 🟢 |
| `src/Components/Products/aside_menu.php` | `produtos/` | 🟢 |
| `src/Components/Product/product_header.php` | `produto/` | 🟢 |
| `src/Components/Product/product_description.php` | `produto/` | 🟢 |
| `src/Components/Product/product_breadcrumb.php` | `produto/` | 🟢 |
| `src/Components/RandomProducts/random_products_cards.php` | `home/`, `produtos/` | 🟢 |

## Migrations

| Arquivo do legado | Unit correspondente | Cobertura |
|-------------------|---------------------|-----------|
| `src/Migrations/1_create_migrations_table.php` | n/a (runner) | n/a |
| `src/Migrations/2_create_test_table.php` | n/a (teste/legado) | n/a |
| `src/Migrations/3_drop_test_table.php` | n/a (teste/legado) | n/a |
| `src/Migrations/4_create_categories_table.php` | `produtos/` | 🟢 |
| `src/Migrations/5_create_products_table.php` | `produtos/`, `produto/` | 🟢 |
| `src/Migrations/6_add_categories_description.php` | `produtos/` | 🟢 |
| `src/Migrations/7_add_product_short_description.php` | `produtos/`, `produto/` | 🟡 (ADR-009) |
| `src/Migrations/8_create_users_table.php` | `login/`, `autenticar/`, `login-admin/`, `autenticar-admin/`, `perfil/`, `atualizar-perfil/` | 🟡 (ADR-008) |
| `src/Migrations/9_create_carts_and_cart_items_tables.php` | `carrinho/`, `carrinho-adicionar/`, `carrinho-atualizar/`, `carrinho-remover/` | 🟢 |
| `src/Migrations/10_create_contacts_table.php` | `enviar-contato/` | 🟢 |

## Notas do revisor

- Cobertura estimada: **46/60 arquivos PHP do legado** mapeados a units (≈ **77%**); os demais são infraestrutura (`n/a`).
- ✅ Resolvido na revisão: `enviar-contato/requirements.md` não referencia mais `src/Services/Contact/ContactService.php` — a lógica é inline em `src/Controllers/About.php:38-93` (referência corrigida).
- ✅ Resolvido na revisão: `home/` e `sobre/` **não** referenciam `Services/Home`/`Services/About` (inexistentes) — usam apenas `Controllers`, `Pages` e `Functions`.
- ✅ Resolvido na revisão: `RandomProductsService` mapeado para `produtos/`+`produto/` (não `home/`), confirmado por `grep` — usado em `Products.php:11` e nos componentes `product.php:33` / `products.php:43`.
- 🔴 Lacunas conhecidas a validar com o usuário: ADR-008 (schema `users`), ADR-010 (`/admin/dashboard`), ADR-009 (migration 7). Ver `_reversa_sdd/questions.md`.
