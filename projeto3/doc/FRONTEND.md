# Frontend Assets & Templating

## Asset Structure

All assets are in the web root under `public/assets/`:

```
public/assets/
  bootstrap/          Bootstrap 5 (CSS + JS, minified + source)
  fontawesome/        Font Awesome 6 (CSS, webfonts, sprites, SCSS)
```

**Note**: Font Awesome includes the full set of source files (`scss/`, `metadata/`, `sprites/`). Only the compiled CSS and webfonts are strictly needed at runtime.

## Templating

The template system is homemade procedural PHP:

1. **View service** (`src/Services/View.php`): Creates a closure that uses `extract()` + output buffering
2. **Variables** are passed as an associative array and become local variables in the template via `extract()`
3. **Components** are included via `require_once` from `src/Components/`
4. **Image assets** are served from `public/images/`

## HTML Structure

All pages follow the same structure:
```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?></title>
  <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/fontawesome/css/all.min.css" rel="stylesheet">
  <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>
  <!-- Navbar (from navbar.php) -->
  <!-- Page content -->
</body>
</html>
```

## Navigation Menu

The navbar in `src/Components/navbar.php`:
- Iterates `$routes` filtered by `getMenuItens()`
- Shows "Gerenciar" dropdown when admin or user is logged in
- Admin dropdown: "Administração" + "Sair"
- User dropdown: "Perfil" + "Sair"
- Active route gets `class="active"`

## Response Strategy

- `$configs['response'](statusCode, content)` — sets Content-length, flushes, runs deferred actions
- `$configs['redirect'](url, statusCode)` — sets Location header
- All output is buffered so headers can be set after rendering
- Deferred actions (like logging) run **after** the response is sent to the client
