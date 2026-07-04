# Middleware System

**Files**: `src/Services/Router.php`, `src/Middlewares/`

## Pipeline Execution

Middlewares are defined as an array in the route definition (`'middlewares' => ['redirectAbout']`). They execute via a recursive closure pipeline:

```php
function executeMiddlewares(array $middlewareStack, array &$configs, array $route, string $uri, Closure $finalCallback): void
{
    $next = function () use (&$middlewareStack, &$configs, $route, $uri, $finalCallback, &$next) {
        if (empty($middlewareStack)) { $finalCallback(); return; }
        $middlewareName = array_pop($middlewareStack);
        $middlewareFunction = $middlewareName . 'Middleware';
        requireMiddleware($middlewareName);
        $middlewareFunction($configs, $route, $uri, $next);
    };
    $next();
}
```

Each middleware receives `($configs, $route, $uri, $next)`. To pass control to the next middleware or the final controller, call `$next()`. To halt the chain, simply return without calling `$next()`.

## Available Middlewares

| File | Function | Behavior |
|------|----------|----------|
| `redirectAbout.php` | `redirectAboutMiddleware()` | Always redirects to `/sobre` without calling `$next()` |

> **Note**: The home route (`/`) currently uses `redirectAbout` middleware, which means visiting `/` will always redirect to `/sobre`. This appears to be a development/test middleware.

## Middleware Naming Convention

- File: `src/Middlewares/{name}.php`
- Function: `{name}Middleware(array &$configs, array $route, string $uri, Closure $next): void`
