# Database Access Layer

**File**: `src/Services/DB.php`

## Connection

Uses `mysqli_*` functions. Connection parameters come from environment variables:

| Env Var | Default | Description |
|---------|---------|-------------|
| `DB_SERVER` | `localhost` | Database host |
| `DB_PORT` | `3306` | Database port |
| `DB_DATABASE` | `projeto` | Database name |
| `DB_USER` | `root` | Database user |
| `DB_PASSWORD` | (empty) | Database password |

## Functions

### `dbConnect(): mysqli`
Creates a new MySQLi connection, sets charset to `utf8mb4`, calls `die()` on failure.

### `dbClose(mysqli $connection): void`
Closes the connection.

### `dbExecuteStm(mysqli $connection, string $stm): mysqli_result|bool`
Executes a raw SQL query.

### `dbPrepareAndExecute(mysqli $connection, string $stm, array $args = []): mysqli_result|bool`
Prepares a statement, binds parameters, executes, returns result.

## Parameter Format

Parameters are passed as an array of arrays:
```php
[
    ['type' => 's', 'value' => $stringValue],
    ['type' => 'i', 'value' => $intValue],
]
```

Supported types: `s` (string), `i` (integer), `d` (double), `b` (blob).

## Usage in Services

The connection is stored in `$configs['connection']` and passed to every service function:
```php
$products = getActiveProducts($configs['connection']);
```

## Database Schema

Tables created by migrations: `migrations`, `categories`, `products`, `users`.

### `categories`
| Column | Type | Notes |
|--------|------|-------|
| id | INT UNSIGNED | PK, AUTO_INCREMENT |
| name | VARCHAR(255) | |
| active | BOOLEAN | |
| description | TEXT | Added in migration 6 |
| created_at | DATETIME | |
| updated_at | DATETIME | on update CURRENT_TIMESTAMP |

### `products`
| Column | Type | Notes |
|--------|------|-------|
| id | INT UNSIGNED | PK, AUTO_INCREMENT |
| name | VARCHAR(255) | |
| description | TEXT | |
| description_line | VARCHAR(150) | |
| short_description | VARCHAR(255) | |
| active | BOOLEAN | |
| stock | INT UNSIGNED | |
| price | INT | Stored in cents |
| image | TEXT | |
| category_id | INT UNSIGNED | FK → categories.id |
| created_at | DATETIME | |
| updated_at | DATETIME | |

### `users`
| Column | Type | Notes |
|--------|------|-------|
| id | INT UNSIGNED | PK, AUTO_INCREMENT |
| name | VARCHAR(255) | |
| active | BOOLEAN | |
| admin | BOOLEAN | |
| created_at | DATETIME | |
| updated_at | DATETIME | |

### `migrations`
| Column | Type |
|--------|------|
| id | INT UNSIGNED |
| name | VARCHAR(255) |
| executed | BOOLEAN |
| created_at | DATETIME |
