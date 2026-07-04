# Page Templates (Views)

**Directory**: `src/Pages/`

Views are plain PHP files that output HTML. The `createView()` service extracts variables into the symbol table and captures output with output buffering.

## Available Pages

| File | Description |
|------|-------------|
| `home.php` | Home page |
| `about.php` | About page |
| `not_found.php` | 404 page |
| `Login/login.php` | Login form page (shared by admin and user login) |
| `Products/products.php` | Product listing with filters |
| `Products/product.php` | Single product detail |
| `Users/profile.php` | User profile with edit form |

## Variable Injection

Variables are passed as an array to `$configs['view']()` and extracted:

```php
$content = $configs['view']('products', [
    'title'    => 'Products',
    'products' => $products,
    'routes'   => getMenuItens($configs['routes'], $uri, $route),
]);
```

In the template, variables are accessed directly: `<?= $title ?>`, `<?php foreach ($products as $product): ?>`

## Template Inheritance

No formal inheritance — pages use `require_once` for header/footer:

```php
<?php require_once COMPONENTS . 'header.php' ?>
<!-- page content -->
<?php require_once COMPONENTS . 'footer.php' ?>
```

The header loads: Bootstrap CSS/JS, Font Awesome, and renders the navbar.
