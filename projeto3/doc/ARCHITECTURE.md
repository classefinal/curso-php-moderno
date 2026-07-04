# Architecture Overview

Custom **procedural PHP framework** (no OOP). Simulates an online store with user and admin areas.

## Request Flow

```
public/index.php
  └─ app.php
       ├─ session_start()
       ├─ ob_start()
       ├─ Load path.php (path helpers)
       ├─ Define directory constants (CONTROLLERS, SERVICES, etc.)
       ├─ Require core services (Router, DB, View, etc.)
       ├─ Load configs (routes.php, events.php)
       ├─ loadEnv(.env)
       ├─ createDefer() → defer/dispatcher
       ├─ dbConnect() → mysqli connection
       ├─ Build $configs array
       ├─ createEventDispatcher()
       ├─ processRoutes($configs)
       │    ├─ resolveRoute(uri, routes)
       │    ├─ requireController(controller)
       │    ├─ executeMiddlewares(middlewares, ...)
       │    └─ controller function($configs, $route, $uri)
       │         └─ $configs['view'](template, args)
       │         └─ $configs['response'](status, content)
       └─ ob_end_clean()
       └─ dbClose()
```

## Directory Convention

| Directory | Purpose |
|-----------|---------|
| `src/Controllers/` | Controller functions that handle routes |
| `src/Services/` | Business logic and infrastructure services |
| `src/Pages/` | View templates (PHP files with HTML) |
| `src/Components/` | Reusable partial templates |
| `src/Configs/` | Route and event definitions |
| `src/Functions/` | Shared utility functions |
| `src/Listeners/` | Event listener functions |
| `src/Middlewares/` | Middleware functions |
| `src/Migrations/` | Database migration files |
| `public/` | Web root (index.php, assets, .htaccess) |
| `logs/` | Failed login attempt logs |

## Core Patterns

- **No OOP**: plain PHP functions and closures throughout
- **$configs array as DI container**: `$configs['connection']`, `['view']`, `['response']`, `['redirect']`, `['defer']`, `['eventDispatcher']`, `['routes']` passed everywhere
- **Closure-based services**: `createView()`, `createResponse()`, `createDefer()` return closures that close over dependencies
- **Output buffering**: `ob_start()`/`ob_get_contents()` allows header manipulation after rendering
- **Deferred execution**: post-response actions queued via `$configs['defer']()` and executed after flush
- **All data shapes documented**: see `types.php` for Psalm type annotations used across the project
- **Require path convention**: `getRequirePath(string)` converts `/` to `DIRECTORY_SEPARATOR` for cross-platform file inclusion
- **Route file naming**: subdirectory-based grouping (e.g. `Login/Login.php`, `Admin/Login/AdminLogin.php`)
