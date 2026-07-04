# Controllers

**Directory**: `src/Controllers/`

Controllers are plain PHP files containing functions. Each function receives `(array $configs, array $route, ?string $uri)`.

## Convention

- File and function names match the `controller` and `call` fields in the route definition
- Controllers require their service dependencies manually via `require_once`
- Controllers interact with `$configs` for: `view()`, `response()`, `redirect()`, `connection`, `eventDispatcher`, `defer`

## Available Controllers

| File | Functions | Route IDs |
|------|-----------|-----------|
| `Home.php` | `makeHome()` | `home` |
| `About.php` | `makeAbout()` | `about` |
| `NotFound.php` | `makeNotFound()` | `notFound` |
| `Login/Login.php` | `makeLogin()`, `validateLogin()`, `logoutLogin()` | `login_page`, `login`, `logout` |
| `Admin/Login/AdminLogin.php` | `makeAdminLogin()`, `validateAdminLogin()`, `logoutAdminLogin()` | `admin_login_page`, `admin_login`, `admin_logout` |
| `Products/Products.php` | `makeProducts()`, `makeProduct()` | `products`, `product` |
| `Users/Users.php` | `viewProfile()`, `updateProfile()` | `user_profile`, `user_profile_update` |

## Typical Controller Pattern

```php
function example(array $configs, array $route, ?string $uri): void
{
    // 1. Auth guard (if needed)
    if (!isset($_SESSION['user'])) { $configs['redirect']('/login'); return; }

    // 2. Call service functions
    $data = someService($configs['connection']);

    // 3. Render view
    $content = $configs['view']('folder/template', [
        'title' => 'Page Title',
        'routes' => getMenuItens($configs['routes'], $uri, $route),
        'data' => $data,
    ]);

    // 4. Send response
    $configs['response'](content: $content);
}
```

## Important Notes

- GET and POST for the same URL are **different route entries** with different `call` functions
- Session checks are done inline, not via middleware
- On validation failure, controllers re-render the form view with an `$error` variable and appropriate HTTP status code (401, 422)
