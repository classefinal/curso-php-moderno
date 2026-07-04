# Frontend Assets & Templating

## Asset Structure

Assets in `public/assets/`:

- `bootstrap/` — Bootstrap 5 (CSS + JS, minified + source)
- `fontawesome/` — Font Awesome 6 (CSS, webfonts, sprites, SCSS — full source included)

Images served from `public/images/`.

## Templating System

Homemade procedural PHP in `src/Services/View.php`:

1. `createView()` returns a closure
2. Closure calls `extract($args)` on the passed array → variables become local symbols
3. `require` the template file, capture with `ob_get_contents()`
4. Returns rendered HTML string

```php
$content = $configs['view']('products', ['title' => 'Products', 'products' => $products, ...]);
```

## HTML Structure

```
DOCTYPE html → head (meta, title, Bootstrap CSS, Font Awesome CSS) → body → navbar → page content → footer
```

All pages include `header.php` (opens `<html>`, loads assets, renders navbar) and `footer.php`.

## Response Strategy

- `$configs['response'](statusCode, content)` — sets Content-Length, flushes, runs deferred actions
- `$configs['redirect'](url, statusCode)` — sets Location header
- All buffered: headers set after rendering
- Deferred actions run after response sent to client

## Navbar Behavior

`src/Components/navbar.php` iterates `$routes` filtered by `getMenuItens()`. Shows "Gerenciar" dropdown when `$_SESSION['user']` or `$_SESSION['admin']` set. Active route gets `class="active"`.
