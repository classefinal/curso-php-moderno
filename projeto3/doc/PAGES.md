# Page Templates (Views)

**Directory**: `src/Pages/`

Plain PHP files outputting HTML. Rendered by `createView()` service via `extract()` + output buffering.

## Pattern

```php
$content = $configs['view']('products', [
    'title'    => 'Products',
    'products' => $products,
    'routes'   => getMenuItens($configs['routes'], $uri, $route),
]);
```

Variables accessed directly in template: `<?= $title ?>`, `<?php foreach ($products as $product): ?>`.

## Template Structure

No formal inheritance. Pages use `require_once` for header/footer components:

```php
<?php require_once COMPONENTS . 'header.php' ?>
<!-- page content -->
<?php require_once COMPONENTS . 'footer.php' ?>
```

Header loads Bootstrap CSS/JS, Font Awesome, and renders navbar.

## Available Pages

See files in `src/Pages/`: `home.php`, `about.php`, `not_found.php`, `Login/login.php`, `Products/products.php`, `Products/product.php`, `Users/profile.php`.
