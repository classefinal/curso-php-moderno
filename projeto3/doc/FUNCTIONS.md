# Shared Functions

**File**: `src/Functions/Functions.php`

## Menu Functions

- `isMenuAllowed(array $route): bool` — Hides login routes when user already authenticated
- `isHomeRoute(array $route, ?string $uri): bool` — True when URI is root and route is home
- `isRouteInAllowedRoutes(array $route, array $currentRoute): bool` — Checks `allowedRoutes` for sub-route highlighting
- `isMenuActive(array $route, ?string $uri, array $currentRoute): bool` — Active state by URI or allowed sub-routes
- `getMenuItens(array $routes, ?string $uri, array $currentRoute): array` — Filters menu-visible routes, sorts by `order`, marks active

## Path Helpers

**File**: `path.php` (loaded in `app.php`)

All path helpers use the pattern `get{Type}Path(): string` returning `BASE_PATH/src/{type}/`. Key helper:

- `getRequirePath(string $path): string` — Converts forward slashes to `DIRECTORY_SEPARATOR` for cross-platform file includes

Path helpers available: `getPath()`, `getComponentsPath()`, `getControllersPath()`, `getFunctionsPath()`, `getPagesPath()`, `getConfigsPath()`, `getServicesPath()`, `getMigrationsPath()`, `getListenersPath()`, `getMiddlewaresPath()`.
