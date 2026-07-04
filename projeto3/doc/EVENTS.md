# Event System

**File**: `src/Services/EventDispatcher.php`

## How It Works

`createEventDispatcher()` receives `$configs` and the `$events` config array. Stores a closure in `$configs['eventDispatcher']`.

### Dispatching

```php
$configs['eventDispatcher']('LoginRecused', [
    'email' => $email,
    'date'  => date('Y-m-d H:i:s'),
]);
```

### Resolution Order

1. Looks up `$events[$eventName]`
2. If no listeners, returns early
3. Iterates each listener entry:
   - `Closure` → called directly
   - `string` → requires listener file from `src/Listeners/`, calls the function

### File Path Resolution

Config key `AdminLogin/AdminLoginErrorListener` → `LISTENERS + getRequirePath('AdminLogin/AdminLoginErrorListener.php')` → `src/Listeners/AdminLogin/AdminLoginErrorListener.php`

## Available Events

Both login-failure events: `AdminLoginRecused` (triggered in `AdminLogin.php:validateAdminLogin()`) and `LoginRecused` (triggered in `Login.php:validateLogin()`). Both pass `['email' => ..., 'date' => ...]` as payload.
