# Services

**Directory**: `src/Services/`

Two categories: **Infrastructure Services** (framework internals) and **Business Services** (domain logic).

## Infrastructure Services

| File | Purpose |
|------|---------|
| `DB.php` | MySQLi connection and query execution (`dbConnect`, `dbClose`, `dbExecuteStm`, `dbPrepareAndExecute`) |
| `Defer.php` | Post-response execution system (`createDefer`) |
| `Environment.php` | `.env` file loader (`loadEnv`) |
| `EventDispatcher.php` | Event dispatch mechanism (`createEventDispatcher`) |
| `Response.php` | `response()` and `redirect()` closures (`createResponse`) |
| `Router.php` | Route processing and middleware pipeline |
| `RouteResolver.php` | URI-to-route matching (`resolveRoute`) |
| `View.php` | PHP template renderer with `extract()` (`createView`) |

## Business Services

| File | Purpose |
|------|---------|
| `Categories/CategoriesService.php` | Category queries (`getActiveCategories`, `getActiveCategoryById`) |
| `Login/LoginService.php` | Login validation and authentication |
| `Products/ProductsService.php` | Product queries with filtering/pagination |
| `Products/RandomProductsService.php` | Random product selection |
| `Users/UsersService.php` | User profile CRUD |

## Key Patterns

- Service functions receive `mysqli $connection` as first parameter
- `$_GET`/`$_POST` read inside service functions (not passed in)
- DB queries use `dbPrepareAndExecute()` with typed param arrays
- Return values: associative arrays with `success`/`error` keys
- PHP 8.1 features in use: first-class callable syntax, arrow functions, `|>` pipe operator
- Business services under subdirectories grouped by domain (`Categories/`, `Login/`, `Products/`, `Users/`)
