# Controllers

**Directory**: `src/Controllers/`

Plain PHP files containing functions. Each receives `(array $configs, array $route, ?string $uri)`.

## Naming Convention

- File and function names match `controller` and `call` fields in the route definition
- Subdirectory grouping: `Login/Login.php`, `Admin/Login/AdminLogin.php`, `Products/Products.php`, `Users/Users.php`

## Pattern

```php
function example(array $configs, array $route, ?string $uri): void
{
    if (!isset($_SESSION['user'])) { $configs['redirect']('/login'); return; }

    $data = someService($configs['connection']);

    $content = $configs['view']('folder/template', [
        'title' => 'Page Title',
        'routes' => getMenuItens($configs['routes'], $uri, $route),
        'data' => $data,
    ]);

    $configs['response'](content: $content);
}
```

## Key Patterns

- Services are required manually via `require_once` inside the controller
- `$configs` provides: `view()`, `response()`, `redirect()`, `connection`, `eventDispatcher`, `defer`
- Auth guards done inline via `$_SESSION['user']` / `$_SESSION['admin']` (no middleware for auth)
- On validation failure, re-render form with `$error` + appropriate HTTP status (401/422)
- GET and POST handlers for the same URL are separate route entries (different `call` values)
