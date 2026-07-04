# Architecture Overview

This is a custom **procedural PHP framework** built from scratch (no OOP). It simulates an online store with user and admin areas.

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

## Directory Structure

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

## Core Concepts

- **No OOP**: everything uses plain PHP functions and closures
- **Dependency Injection via $configs**: an associative array is passed everywhere containing `connection`, `view`, `response`, `redirect`, `defer`, `eventDispatcher`, and `routes`
- **Closure-based services**: services like `createView()`, `createResponse()`, `createDefer()` return closures that close over their dependencies
- **Output buffering**: all output is captured with `ob_start()`/`ob_get_contents()` to allow HTTP header manipulation before content is sent
- **Deferred execution**: post-response actions can be queued via `$configs['defer']()` and executed after the response is flushed
