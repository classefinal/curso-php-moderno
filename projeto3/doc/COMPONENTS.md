# Components

**Directory**: `src/Components/`

Reusable partial templates included from pages or other components via `require_once`.

## Layout (`src/Components/`)

- `header.php` — DOCTYPE, `<head>`, Bootstrap/FontAwesome, navbar include
- `footer.php` — Closes `<body>` and `<html>`
- `navbar.php` — Bootstrap navbar, dynamic menu from `$routes`, admin/user dropdown when logged in

## Auth (`src/Components/Login/`)

- `login_form.php` — Email/password form, posts to `$action`

## Products (`src/Components/Products/`)

- `aside_menu.php` — Sidebar: item count filter + category accordion
- `categories_accordion_list.php` — Category filter links
- `product_card.php` — Bootstrap card (image, name, price, buttons)
- `products_list.php` — Grid of cards or empty state

## Product Detail (`src/Components/Product/`)

- `product_breadcrumb.php` — Category breadcrumb
- `product_header.php` — Image, name, short description, price, stock badge, buy button
- `product_description.php` — Full description

## Random Products (`src/Components/RandomProducts/`)

- `random_products_cards.php` — Horizontal strip of random product cards

## Utility (`src/Components/Empty/`)

- `empty.php` — Empty state with image, title, subtitle, optional CTA link

## Conventions

- Variables received via `extract()` from parent page
- Use `getRequirePath()` for forward-slash paths → `DIRECTORY_SEPARATOR`
- Some components include Psalm type annotations (see `types.php`)
