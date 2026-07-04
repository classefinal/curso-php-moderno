# Database Migrations

**File**: `migrate.php` (CLI runner), `src/Migrations/` (migration files)

## CLI Usage

```bash
php migrate.php
```

## Runner Logic

1. Connects to DB via env vars
2. Scans `src/Migrations/` for `.php` files
3. Sorts alphabetically (by numeric prefix)
4. Checks `migrations` tracking table exists (runs migration 1 if not)
5. For each migration, checks if already executed, runs `$migration['up']($connection)`, records in `migrations` table

## Migration Format

Each file returns an array with an `up` closure:

```php
$migration = [
    'up' => function(mysqli $connection): void {
        dbExecuteStm($connection, "CREATE TABLE ...");
    }
];
return $migration;
```

## Migration Files

Files in `src/Migrations/` prepended with numeric order (e.g. `1_create_migrations_table.php`). See the directory for the full list and execution order.

## Known Issue

Migration 8 creates `users` table with columns `id, name, active, admin, created_at, updated_at` but INSERTs into `name, email, password, active, admin`. Columns `email` and `password` are not in the CREATE TABLE.
