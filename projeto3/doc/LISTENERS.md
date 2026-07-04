# Listeners

**Directory**: `src/Listeners/`

Functions that handle dispatched events.

## Available Listeners

| File | Function | Handles Event |
|------|----------|--------------|
| `AdminLogin/AdminLoginErrorListener.php` | `handleAdminLoginErrorEvent()` | `AdminLoginRecused` |
| `Login/LoginErrorListener.php` | `handleLoginErrorEvent()` | `LoginRecused` |

## Listener Pattern

```php
function handleXxxEvent(array $configs, array $args): void
{
    if (empty($args['email']) || empty($args['date'])) { return; }
    $configs['defer'](function () use ($args) {
        // Write to a dated log file
        $folder = BASE_PATH . DIRECTORY_SEPARATOR . 'logs';
        if (!file_exists($folder) && !mkdir($folder)) { return; }
        file_put_contents(
            $folder . DIRECTORY_SEPARATOR . date('Y-m-d') . '-xxxErrors.txt',
            "{$args['date']}: {$args['email']}" . PHP_EOL,
            FILE_APPEND
        );
    });
}
```

## Key Pattern: Deferred Execution

Both listeners use `$configs['defer']()` to queue a file write operation that runs **after** the HTTP response is sent. This ensures login failure logging doesn't slow down the user-facing response.

## Naming Convention

- File name matches the key in `events.php` config
- Function name matches the value in `events.php` config
- Files are auto-required by the event dispatcher when an event fires
