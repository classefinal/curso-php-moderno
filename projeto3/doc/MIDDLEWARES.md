# Middleware System

**Files**: `src/Services/Router.php`, `src/Middlewares/`

## Pipeline Execution

Middlewares defined as `'middlewares' => ['redirectAbout']` in route definition. Executed via recursive closure:

```php
function executeMiddlewares(array $middlewareStack, array &$configs, array $route, string $uri, Closure $finalCallback): void
```

Each middleware receives `($configs, $route, $uri, $next)`. Call `$next()` to continue chain, return without calling to halt.

## Naming Convention

- File: `src/Middlewares/{name}.php`
- Function: `{name}Middleware(array &$configs, array $route, string $uri, Closure $next): void`

## Available Middleware

- `redirectAbout.php` → `redirectAboutMiddleware()` — Always redirects to `/sobre` (used on home route)

> **Note**: The home route (`/`) currently uses `redirectAbout` middleware, redirecting to `/sobre`. Likely a development/test config.
