# Services

**Directory**: `src/Services/`

Two categories: **Infrastructure Services** (framework internals) and **Business Services** (domain logic).

## Infrastructure Services

| File | Function(s) | Purpose |
|------|-------------|---------|
| `DB.php` | `dbConnect()`, `dbClose()`, `dbExecuteStm()`, `dbPrepareAndExecute()` | MySQLi database connection and query execution |
| `Defer.php` | `createDefer()` | Creates a defer/dispatcher system for post-response execution |
| `Environment.php` | `loadEnv()` | Loads `.env` file, sets environment variables |
| `EventDispatcher.php` | `createEventDispatcher()` | Creates event dispatch mechanism |
| `Response.php` | `createResponse()` | Creates `response()` and `redirect()` closures |
| `Router.php` | `requireController()`, `requireMiddleware()`, `executeMiddlewares()`, `processRoutes()` | Route processing and middleware pipeline |
| `RouteResolver.php` | `resolveRoute()` | Matches a URI against the route table |
| `View.php` | `createView()` | Creates a closure that renders PHP templates with `extract()` |

## Business Services

| File | Functions | Purpose |
|------|-----------|---------|
| `Categories/CategoriesService.php` | `getActiveCategories()`, `getActiveCategoryById()` | Category queries |
| `Login/LoginService.php` | `validateLoginInfo()`, `loginAuthenticate()`, `adminLoginAuthenticate()` | Login validation and authentication |
| `Products/ProductsService.php` | `getActiveProducts()`, `getProductById()`, `getActiveProductsParams()`, `getActiveProductsQuery()` | Product queries with filtering/pagination |
| `Products/RandomProductsService.php` | `getRandomActiveProducts()` | Random product selection for featured section |
| `Users/UsersService.php` | `getUserById()`, `updateUserProfile()`, `validateUpdateUserPassword()`, `setUpdatedUserIntoSession()` | User profile management |

## Key Patterns

- Service functions receive `mysqli $connection` as first parameter
- Parameters are read from `$_GET`/`$_POST` inside service functions
- Database queries use `dbPrepareAndExecute()` with typed parameter arrays:
  ```php
  dbPrepareAndExecute($connection, 'SELECT * FROM table WHERE id = ?',
      [['type' => 'i', 'value' => $id]]);
  ```
- Return values are associative arrays with `success`/`error` keys
- PHP 8.1 first-class callable syntax and arrow functions are used
- `|>` pipe operator is used in some places
