# Configurations

**Directory**: `src/Configs/`

## Routes (`routes.php`)

Returns an array of route definitions. See [Routing](ROUTING.md) for format.

Contains **14 routes**:
- Home, About, Products, Product detail
- Admin login page, admin login action, admin logout
- User login page, login action, logout
- User profile view, profile update

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

Each key is an event name. Each value is an associative array where:
- Key: relative path to listener file (without `.php`) under `src/Listeners/`
- Value: the function name to call in that file

### How Events Are Loaded

Events are loaded in `app.php` with:
```php
$events = require_once CONFIGS . 'events.php';
```

The `$events` array is passed to `createEventDispatcher()`, which stores a closure in `$configs['eventDispatcher']`. When an event is dispatched, the dispatcher iterates the listeners for that event name, requires the listener file, and calls the registered function.
