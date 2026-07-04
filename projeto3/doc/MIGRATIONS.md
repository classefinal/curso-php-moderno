# Database Migrations

**File**: `migrate.php` (CLI runner), `src/Migrations/` (migration files)

## CLI Usage

```bash
php migrate.php
```

The runner:
1. Connects to the database using env vars
2. Scans `src/Migrations/` for PHP files
3. Sorts them alphabetically (by number prefix)
4. Checks if the `migrations` table exists
5. Runs the base migration (`1_create_migrations_table.php`) first if needed, which creates the tracking table
6. For each subsequent migration, checks if already executed, and runs it via `$migration['up']($connection)`
7. Records execution in the `migrations` table

## Migration Format

Each migration file returns an array:

```php
$migration = [
    'up' => function(mysqli $connection): void {
        dbExecuteStm($connection, "CREATE TABLE ...");
    }
];
return $migration;
```

## Migration History

| # | File | Purpose |
|---|------|---------|
| 1 | `1_create_migrations_table.php` | Creates the `migrations` tracking table |
| 2 | `2_create_test_table.php` | Creates a `test` table (dev/testing) |
| 3 | `3_drop_test_table.php` | Drops the `test` table |
| 4 | `4_create_categories_table.php` | Creates `categories` table |
| 5 | `5_create_products_table.php` | Creates `products` table with FK to `categories` |
| 6 | `6_add_categories_description.php` | Adds `description` column to `categories` |
| 7 | `7_add_product_short_description.php` | Adds `description_line` and `short_description` to `products` |
| 8 | `8_create_users_table.php` | Creates `users` table with a default admin user |

## Known Issue

Migration 8 creates a `users` table with columns: `id, name, active, admin, created_at, updated_at` but tries to INSERT values into `name, email, password, active, admin`. The `email` and `password` columns are not defined in the CREATE TABLE statement.
