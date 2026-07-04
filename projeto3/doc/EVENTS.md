# Event System

**File**: `src/Services/EventDispatcher.php`

## How It Works

`createEventDispatcher()` receives the `$configs` array and the `$events` configuration array. It stores a closure in `$configs['eventDispatcher']`.

### Dispatching an Event

```php
$configs['eventDispatcher']('LoginRecused', [
    'email' => $email,
    'date'  => date('Y-m-d H:i:s'),
]);
```

### Dispatching Logic

1. Looks up `$events[$eventName]`
2. If no listeners registered for that event, returns early
3. Iterates each listener entry:
   - If the value is a `Closure`, it's called directly
   - If it's a string (function name), requires the listener file from `LISTENERS . $listenerPath` and calls the function

### Listener Resolution

The listener config key (e.g. `AdminLogin/AdminLoginErrorListener`) is converted to a file path:
```
LISTENERS + getRequirePath('AdminLogin/AdminLoginErrorListener.php')
```
Which resolves to: `src/Listeners/AdminLogin/AdminLoginErrorListener.php`

## Available Events

| Event Name | Triggered In | Payload |
|------------|-------------|---------|
| `AdminLoginRecused` | `AdminLogin.php:validateAdminLogin()` | `email`, `date` |
| `LoginRecused` | `Login.php:validateLogin()` | `email`, `date` |
