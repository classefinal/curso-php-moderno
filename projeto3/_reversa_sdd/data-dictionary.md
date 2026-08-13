# Dicionário de Dados — projeto3

> Gerado pelo **Arqueólogo** em 2026-08-12.
> Escala de confiança: 🟢 CONFIRMADO (migration/código) | 🟡 INFERIDO | 🔴 LACUNA
> Banco: MySQL/MariaDB (mysqli), charset `utf8mb4`/`utf8mb4_unicode_ci`, engine InnoDB.

## Relacionamentos (ERD resumido)

```
users 1 ─── 1 carts (user_id, UNIQUE) ─── * cart_items (cart_id)
categories 1 ─── * products (category_id, ON DELETE CASCADE)
products 1 ─── * cart_items (product_id, ON DELETE CASCADE)
contacts (independente)
migrations (controle do runner)
```

## migrations

Tabela de controle do runner (`migrate.php`).

| Campo | Tipo | Obrigatório | Padrão | Observação |
|-------|------|-------------|--------|------------|
| id | INT UNSIGNED | sim | AUTO_INCREMENT | PK |
| name | VARCHAR(255) | sim | — | Nome do arquivo da migration |
| executed | BOOLEAN | sim | — | 1 = executada |
| created_at | DATETIME | sim | CURRENT_TIMESTAMP | |

🟢 Fonte: `src/Migrations/1_create_migrations_table.php`.

## categories

| Campo | Tipo | Obrigatório | Padrão | Observação |
|-------|------|-------------|--------|------------|
| id | INT UNSIGNED | sim | AUTO_INCREMENT | PK |
| name | VARCHAR(255) | sim | — | |
| active | BOOLEAN | sim | — | Filtro de exibição |
| description | TEXT | sim | `''` | Adicionada pela migration 6 (`AFTER active`) |
| created_at | DATETIME | sim | CURRENT_TIMESTAMP | |
| updated_at | DATETIME | sim | CURRENT_TIMESTAMP | `ON UPDATE CURRENT_TIMESTAMP` |

🟢 Fonte: `src/Migrations/4_create_categories_table.php` + `6_add_categories_description.php`.

## products

| Campo | Tipo | Obrigatório | Padrão | Observação |
|-------|------|-------------|--------|------------|
| id | INT UNSIGNED | sim | AUTO_INCREMENT | PK |
| name | VARCHAR(255) | sim | — | |
| description | TEXT | sim | — | Descrição completa |
| active | BOOLEAN | sim | — | Filtro de exibição |
| stock | INT UNSIGNED | sim | — | Estoque |
| price | INT | sim | — | **Em centavos** (exibido dividido por 100) |
| image | TEXT | sim | — | Caminho/URL da imagem |
| category_id | INT UNSIGNED | sim | — | FK → categories.id (ON DELETE/UPDATE CASCADE) |
| short_description | VARCHAR(255) | sim | `''` | Migration 7 |
| description_line | VARCHAR(150) | sim | `''` | Migration 7 |
| created_at | DATETIME | sim | CURRENT_TIMESTAMP | |
| updated_at | DATETIME | sim | CURRENT_TIMESTAMP | `ON UPDATE CURRENT_TIMESTAMP` |

> ⚠️ **🔴 LACUNA:** a migration 7 (`7_add_product_short_description.php`) executa `ADD short_description ... AFTER description_line, ADD description_line ... AFTER short_description` — ambas as colunas de referência são criadas no mesmo comando; em um banco limpo o ALTER tende a falhar por coluna inexistente. O schema real do banco pode ter evoluído manualmente.

🟢 Fonte: `src/Migrations/5_create_products_table.php` + `7_add_product_short_description.php`.

## users

| Campo | Tipo | Obrigatório | Padrão | Observação |
|-------|------|-------------|--------|------------|
| id | INT UNSIGNED | sim | AUTO_INCREMENT | PK |
| name | VARCHAR(255) | sim | — | |
| active | BOOLEAN | sim | — | Bloqueio de acesso |
| admin | BOOLEAN | sim | — | Separa login comum (false) de admin (true) |
| created_at | DATETIME | sim | CURRENT_TIMESTAMP | |
| updated_at | DATETIME | sim | CURRENT_TIMESTAMP | `ON UPDATE CURRENT_TIMESTAMP` |

> ⚠️ **🔴 LACUNA CRÍTICA:** a migration 8 cria `users` **sem** `email` e `password`, mas o INSERT da própria migration e todo o código (login, perfil) usam essas colunas. Colunas necessárias para o sistema funcionar:
>
> | Campo | Tipo | Observação |
> |-------|------|------------|
> | email | VARCHAR(255) | Usado em `WHERE email = ?` (login) e exibido no perfil |
> | password | VARCHAR(255) | Hash bcrypt (`password_hash`/`password_verify`) |
>
> 🟡 INFERIDO (necessário pelo código; ausente nas migrations).

🟢/🔴 Fonte: `src/Migrations/8_create_users_table.php` (código: `LoginService.php`, `UsersService.php`, `Pages/Users/profile.php`).
**Seed de admin (migration 8):** `name='Administrador'`, `email='admin@admin.com'`, `password=password_hash('admin123', PASSWORD_BCRYPT, cost 16)`, `active=1`, `admin=1`.

## carts

| Campo | Tipo | Obrigatório | Padrão | Observação |
|-------|------|-------------|--------|------------|
| id | INT UNSIGNED | sim | AUTO_INCREMENT | PK |
| user_id | INT | sim | — | UNIQUE KEY (1 carrinho por usuário); FK → users.id (CASCADE) |
| created_at | DATETIME | sim | CURRENT_TIMESTAMP | |
| updated_at | DATETIME | sim | CURRENT_TIMESTAMP | `ON UPDATE CURRENT_TIMESTAMP` |

🟢 Fonte: `src/Migrations/9_create_carts_and_cart_items_tables.php`.

## cart_items

| Campo | Tipo | Obrigatório | Padrão | Observação |
|-------|------|-------------|--------|------------|
| id | INT UNSIGNED | sim | AUTO_INCREMENT | PK |
| cart_id | INT UNSIGNED | sim | — | FK → carts.id (CASCADE) |
| product_id | INT UNSIGNED | sim | — | FK → products.id (CASCADE) |
| quantity | INT UNSIGNED | sim | 1 | Incrementa/decrementa |
| created_at | DATETIME | sim | CURRENT_TIMESTAMP | |
| updated_at | DATETIME | sim | CURRENT_TIMESTAMP | `ON UPDATE CURRENT_TIMESTAMP` |

🟢 Fonte: `src/Migrations/9_create_carts_and_cart_items_tables.php`.

## contacts

| Campo | Tipo | Obrigatório | Padrão | Observação |
|-------|------|-------------|--------|------------|
| id | INT UNSIGNED | sim | AUTO_INCREMENT | PK |
| name | VARCHAR(255) | sim | — | |
| email | VARCHAR(255) | sim | — | |
| phone | VARCHAR(20) | sim | — | Normalizado: `+55` + dígitos |
| created_at | DATETIME | sim | CURRENT_TIMESTAMP | |

🟢 Fonte: `src/Migrations/10_create_contacts_table.php`.

## test (histórica — removida)

Tabela `test` criada pela migration 2 e removida pela migration 3. Não existe no schema final. 🟢

## Estruturas em memória (types.php)

- **Route**: `id`, `value`, `controller`, `call`, `isRegex`, `inMenu`, `label`, `order`, `active`, `allowedRoutes`, `methods`, `middlewares`.
- **Configs**: `routes`, `connection`, `defer`, `response`, `redirect`, `eventDispatcher`, `view`, `user`.
- **User**: `id`, `name`, `active`, `admin`, `password`, `created_at`, `updated_at` (psalm-type **não** inclui `email`, embora o código use — 🔴).
- **Product**: `id`, `name`, `description`, `short_description`, `description_line`, `active`, `price`, `stock`, `image`, `category_id`, `category_name`, `created_at`, `updated_at`.
- **Category**: `id`, `name`, `active`, `description`, `created_at`, `updated_at`.
- **CartItem**: `id`, `cart_id`, `product_id`, `quantity`, `name`, `price`, `image`, `stock`, `description_line`, `created_at`, `updated_at`.
- **StmArg**: `type` ('s'|'i'), `value`.
- **LoginInfo**: `success`, `error`.
- **Migration**: `up` (closure).

## Convenções de dados

- **Moeda:** valores armazenados em **centavos** (INT); formatação `number_format($x/100, 2, ',', '.')` → `R$ 1.234,56`.
- **Booleans:** colunas `BOOLEAN` (tinyint 0/1); filtros de exibição `active = true`/`active = 1`.
- **Datas:** `DATETIME` com `CURRENT_TIMESTAMP`; `updated_at` com `ON UPDATE`.
- **Integridade:** FKs com `ON DELETE CASCADE ON UPDATE CASCADE` (products→categories, carts→users, cart_items→carts/products).
