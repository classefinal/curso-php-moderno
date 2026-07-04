# Configurations

**Directory**: `src/Configs/`

## Routes (`routes.php`)

Returns an array of route definitions. See [Routing](ROUTING.md) for the definition format.

Route entries cover: home, about, products, product detail, admin login/logout, user login/logout, user profile.

Key patterns:
- GET and POST for the same URL are separate route entries with different `call` functions
- `inMenu` flag controls navbar visibility; `allowedRoutes` keeps parent menu highlighted on sub-pages

## Events (`events.php`)

Returns an array mapping event names to listeners:

```php
$events = [
    'AdminLoginRecused' => [
        'AdminLogin/AdminLoginErrorListener' => 'handleAdminLoginErrorEvent'
    ],
    'LoginRecused' => [
        'Login/LoginErrorListener' => 'handleLoginErrorEvent'
    ],
];
```

Each key is an event name. Each value is `['ListenerFilePath' => 'functionName']`.
- Key: relative path under `src/Listeners/` (no `.php` extension)
- Value: function name in that file

### Loading

```php
$events = require_once CONFIGS . 'events.php';
```

Passed to `createEventDispatcher()`, which stores a closure in `$configs['eventDispatcher']`. When dispatched, iterates listeners, requires the file, calls the function. Supports both closure and string-based listeners.
