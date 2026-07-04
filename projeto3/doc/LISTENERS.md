# Listeners

**Directory**: `src/Listeners/`

Functions that handle dispatched events.

## Pattern

```php
function handleXxxEvent(array $configs, array $args): void
{
    if (empty($args['email']) || empty($args['date'])) { return; }
    $configs['defer'](function () use ($args) {
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

## Naming Convention

- File name matches the key in `events.php`
- Function name matches the value in `events.php`
- File is auto-required by event dispatcher when event fires

## Available Listeners

- `AdminLogin/AdminLoginErrorListener.php` → `handleAdminLoginErrorEvent()` → handles `AdminLoginRecused`
- `Login/LoginErrorListener.php` → `handleLoginErrorEvent()` → handles `LoginRecused`

## Key Pattern: Deferred Execution

Both listeners use `$configs['defer']()` to queue file writes **after** HTTP response is sent, so logging doesn't slow response.
