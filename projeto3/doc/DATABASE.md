# Database Access Layer

**File**: `src/Services/DB.php`

## Connection

Uses `mysqli_*` functions. Configured via environment variables (see `.env`):

- `DB_SERVER` (default: `localhost`), `DB_PORT` (default: `3306`), `DB_DATABASE` (default: `projeto`), `DB_USER` (default: `root`), `DB_PASSWORD` (default: empty)

## Functions

- `dbConnect()` — Creates MySQLi connection, sets `utf8mb4`, dies on failure
- `dbClose(mysqli $connection)` — Closes connection
- `dbExecuteStm(mysqli $connection, string $stm)` — Executes raw SQL
- `dbPrepareAndExecute(mysqli $connection, string $stm, array $args)` — Prepared statement with typed params

## Parameter Format

```php
$args = [
    ['type' => 's', 'value' => $stringValue],
    ['type' => 'i', 'value' => $intValue],
];
```

Types: `s` (string), `i` (integer), `d` (double), `b` (blob).

## Usage Pattern

```php
$products = getActiveProducts($configs['connection']);
```

Connection stored in `$configs['connection']`, passed to every service function.

## Schema

Tables created by migrations (see [MIGRATIONS.md](MIGRATIONS.md)): `migrations`, `categories`, `products`, `users`. Detailed column definitions are in the migration files at `src/Migrations/`.

### Quick Reference

- **categories**: `id, name, active, description, created_at, updated_at`
- **products**: `id, name, description, description_line, short_description, active, stock, price (cents), image, category_id (FK), created_at, updated_at`
- **users**: `id, name, active, admin, created_at, updated_at`
- **migrations**: `id, name, executed, created_at`
