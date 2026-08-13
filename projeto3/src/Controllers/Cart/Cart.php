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
        $result = addToCart($configs['connection'], (int)$_SESSION['user']['id'], $productId);

        if (!$result['success']) {
            $_SESSION['flash']['error'] = $result['error'] ?? 'Erro ao adicionar item ao carrinho';
        }
    } else {
        addToCartCookie($configs['connection'], $productId);
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
        $result = updateCartItemQuantity($configs['connection'], (int)$_SESSION['user']['id'], $productId, $action);

        if (!$result['success']) {
            $_SESSION['flash']['error'] = $result['error'] ?? 'Erro ao atualizar o carrinho';
        }
    } else {
        updateCartItemQuantityCookie($configs['connection'], $productId, $action);
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
        $result = removeCartItem($configs['connection'], (int)$_SESSION['user']['id'], $productId);

        if (!$result['success']) {
            $_SESSION['flash']['error'] = $result['error'] ?? 'Erro ao remover item do carrinho';
        }
    } else {
        removeCartItemCookie($productId);
    }

    $configs['redirect']('/carrinho', 302);
}
