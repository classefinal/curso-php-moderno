# Components

**Directory**: `src/Components/`

Reusable partial templates included from pages or other components via `require_once`.

## Layout Components

| File | Description |
|------|-------------|
| `header.php` | DOCTYPE, `<head>`, Bootstrap/FontAwesome assets, navbar include |
| `footer.php` | Closes `<body>` and `<html>` |
| `navbar.php` | Bootstrap navbar with dynamic menu items from `$routes`, plus admin/user dropdown when logged in |

## Auth Components

| File | Description |
|------|-------------|
| `Login/login_form.php` | Email/password form, posts to `$action` |

## Product Components

| File | Description |
|------|-------------|
| `Products/aside_menu.php` | Sidebar with item count filter and category accordion |
| `Products/categories_accordion_list.php` | Category links for filtering |
| `Products/product_card.php` | Bootstrap card for a single product (image, name, price, buttons) |
| `Products/products_list.php` | Grid of product cards, or empty state component |
| `Product/product_breadcrumb.php` | Category breadcrumb link |
| `Product/product_header.php` | Product image, name, short description, price, stock badge, buy button |
| `Product/product_description.php` | Full product description |
| `RandomProducts/random_products_cards.php` | Horizontal strip of random product cards |

## Utility Components

| File | Description |
|------|-------------|
| `Empty/empty.php` | Empty state with image, title, subtitle, and optional CTA link |

## Conventions

- Components receive variables via `extract()` from the parent page
- When requiring components, use `getRequirePath()` to convert forward slashes to `DIRECTORY_SEPARATOR`
- Some components include Psalm type annotations for IDE support
