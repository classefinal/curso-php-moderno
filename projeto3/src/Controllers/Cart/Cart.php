<?php

declare(strict_types=1);

require_once SERVICES . getRequirePath('Cart/CartService.php');

/**
 * @psalm-import-type Route from types
 * @psalm-import-type Configs from types
 */

function makeCart(array $configs, array $route, ?string $uri): void
{
    $items = [];
    $total = 0;
    $isLoggedIn = isset($_SESSION['user']);

    if ($isLoggedIn) {
        $user = $_SESSION['user'];
        $cart = getCartByUserId($configs['connection'], (int)$user['id']);

        if ($cart) {
            $items = getCartItems($configs['connection'], $cart['id']);
            $total = calculateCartTotal($items);
        }
    } else {
        $cookieItems = getCartItemsFromCookie();

        if (!empty($cookieItems)) {
            $items = enrichCartItemsWithProductData($configs['connection'], $cookieItems);
            $total = calculateCartTotal($items);
        }
    }

    $content = $configs['view']('Cart/cart', [
        'title' => 'Carrinho de compras',
        'routes' => getMenuItens($configs['routes'], $uri, $route),
        'items' => $items,
        'total' => $total,
    ]);

    $configs['response'](content: $content);
}

function doAddToCart(array $configs, array $route, ?string $uri): void
{
    $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'default' => null]
    ]);

    if (!$productId) {
        $configs['redirect']('/produtos', 302);
        return;
    }

    if (isset($_SESSION['user'])) {
        addToCart($configs['connection'], (int)$_SESSION['user']['id'], $productId);
    } else {
        addToCartCookie($productId);
    }

    $configs['redirect']('/carrinho', 302);
}

function doUpdateCartQuantity(array $configs, array $route, ?string $uri): void
{
    $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'default' => null]
    ]);
    $action = $_POST['action'] ?? '';

    if (!$productId || !in_array($action, ['increase', 'decrease'])) {
        $configs['redirect']('/carrinho', 302);
        return;
    }

    if (isset($_SESSION['user'])) {
        updateCartItemQuantity($configs['connection'], (int)$_SESSION['user']['id'], $productId, $action);
    } else {
        updateCartItemQuantityCookie($productId, $action);
    }

    $configs['redirect']('/carrinho', 302);
}

function doRemoveCartItem(array $configs, array $route, ?string $uri): void
{
    $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'default' => null]
    ]);

    if (!$productId) {
        $configs['redirect']('/carrinho', 302);
        return;
    }

    if (isset($_SESSION['user'])) {
        removeCartItem($configs['connection'], (int)$_SESSION['user']['id'], $productId);
    } else {
        removeCartItemCookie($productId);
    }

    $configs['redirect']('/carrinho', 302);
}
