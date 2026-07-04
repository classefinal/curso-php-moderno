# Routing System

**Files**: `src/Services/Router.php`, `src/Services/RouteResolver.php`, `src/Configs/routes.php`

## Route Definition Format

```php
[
    'id'             => 'home',
    'value'          => '/',                              // URL path or regex
    'controller'     => 'Home',                           // File name under src/Controllers/ (no .php)
    'call'           => 'makeHome',                       // Function name to call
    'isRegex'        => false,                            // Whether value is a regex pattern
    'methods'        => ['GET'],                          // Allowed HTTP methods
    'inMenu'         => true,                             // Show in navigation
    'label'          => 'Home',                           // Navigation label
    'order'          => 0,                                // Menu sort order
    'allowedRoutes'  => ['product'],                      // Sub-routes keeping this menu active
    'middlewares'    => ['redirectAbout'],                 // Middleware stack
]
```

## Resolution

1. `processRoutes()` gets `REQUEST_URI`, parses with `parse_url(..., PHP_URL_PATH)`, normalizes (remove trailing slash, preserve `/`)
2. `resolveRoute()` iterates routes, matching by:
   - HTTP method (`in_array($_SERVER['REQUEST_METHOD'], $route['methods'])`)
   - Exact string match (`isRegex === false`)
   - `preg_match` (`isRegex === true`)
3. On match: require controller file, invoke callable
4. On no match: `NotFound` controller renders 404

## Middleware Pipeline

```php
executeMiddlewares($route['middlewares'], $configs, $route, $uri, function() use ($route, $configs, $uri) {
    $route['call']($configs, $route, $uri);
});
```

Each middleware can halt chain by not calling `$next()`.

## Key Patterns

- Same URL, different HTTP methods → separate route entries with different `call` values
- `getMenuItens()` filters routes by `inMenu` flag and marks active by URI/`allowedRoutes`
- Route file uses subdirectory grouping matching controller structure
