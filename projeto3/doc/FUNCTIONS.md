# Shared Functions

**File**: `src/Functions/Functions.php`

## Menu Functions

### `isMenuAllowed(array $route): bool`
Checks if a route should appear in the navigation menu. Hides login routes when user is already authenticated.

### `isHomeRoute(array $route, ?string $uri): bool`
Returns true when the current URI is empty (root) and the route is the home route.

### `isRouteInAllowedRoutes(array $route, array $currentRoute): bool`
Checks if the current route is in the `allowedRoutes` list of a menu route (used to highlight "Produtos" in menu when viewing a single product).

### `isMenuActive(array $route, ?string $uri, array $currentRoute): bool`
Determines if a menu item should be marked active based on URI or allowed sub-routes.

### `getMenuItens(array $routes, ?string $uri, array $currentRoute): array`
Filters routes to only include menu-visible items, sorts by `order`, marks the active one.

## Path Functions

**File**: `path.php`

| Function | Returns |
|----------|---------|
| `getPath(string $folder)` | `BASE_PATH/src/{folder}/` |
| `getComponentsPath()` | Path to `src/Components/` |
| `getControllersPath()` | Path to `src/Controllers/` |
| `getFunctionsPath()` | Path to `src/Functions/` |
| `getPagesPath()` | Path to `src/Pages/` |
| `getConfigsPath()` | Path to `src/Configs/` |
| `getServicesPath()` | Path to `src/Services/` |
| `getMigrationsPath()` | Path to `src/Migrations/` |
| `getListenersPath()` | Path to `src/Listeners/` |
| `getMiddlewaresPath()` | Path to `src/Middlewares/` |
| `getRequirePath(string)` | Converts `/` to `DIRECTORY_SEPARATOR` |
