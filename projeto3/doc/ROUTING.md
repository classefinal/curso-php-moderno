# Routing System

**Files**: `src/Services/Router.php`, `src/Services/RouteResolver.php`, `src/Configs/routes.php`

## Route Definition

Routes are defined as arrays in `src/Configs/routes.php`:

```php
[
    'id'             => 'home',
    'value'          => '/',                              // URL path or regex
    'controller'     => 'Home',                           // Controller file name (without .php)
    'call'           => 'makeHome',                       // Function name to call
    'isRegex'        => false,                            // Whether value is a regex pattern
    'methods'        => ['GET'],                          // Allowed HTTP methods
    'inMenu'         => true,                             // Show in navigation
    'label'          => 'Home',                           // Navigation label
    'order'          => 0,                                // Menu sort order
    'allowedRoutes'  => ['product'],                      // Sub-routes that keep this menu active
    'middlewares'    => ['redirectAbout'],                 // Middleware stack
]
```

## Route Resolution

1. `processRoutes()` gets the `REQUEST_URI`, parses it, and normalizes it (removes trailing slash)
2. `resolveRoute()` iterates all routes, matching by:
   - HTTP method (`in_array($_SERVER['REQUEST_METHOD'], $route['methods'])`)
   - Exact string match (if `isRegex === false`)
   - `preg_match` (if `isRegex === true`)
3. On match, the controller file is required and the callable function is invoked
4. On no match, the `NotFound` controller renders a 404

## Middleware Execution

Middlewares execute in a pipeline before the controller:

```php
executeMiddlewares($route['middlewares'], $configs, $route, $uri, function() use ($route, $configs, $uri) {
    $route['call']($configs, $route, $uri);
});
```

Each middleware can stop the chain by not calling `$next()`.

## URI Handling

- URI is parsed with `parse_url($uri, PHP_URL_PATH)`
- Trailing slash is removed (`rtrim($parsedUri, "/")`)
- The root path `/` is preserved as-is
- Same URL can have different handlers for GET and POST (e.g., `/login` GET → `makeLogin`, POST → `validateLogin`)
