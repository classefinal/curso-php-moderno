<?php

/**
 * @psalm-import-type CartItem from Types
 */

// ─── DB functions (logged-in users) ───────────────────────────────────

function getCartByUserId(mysqli $connection, int $userId): ?array
{
    $result = dbPrepareAndExecute(
        $connection,
        'SELECT * FROM carts WHERE user_id = ? LIMIT 1',
        [
            ['type' => 'i', 'value' => $userId]
        ]
    );

    if (mysqli_num_rows($result) === 0) {
        return null;
    }

    return mysqli_fetch_assoc($result);
}

function createCart(mysqli $connection, int $userId): array
{
    dbPrepareAndExecute(
        $connection,
        'INSERT INTO carts (user_id) VALUES (?)',
        [
            ['type' => 'i', 'value' => $userId]
        ]
    );

    return [
        'id' => mysqli_insert_id($connection),
        'user_id' => $userId,
    ];
}

function getOrCreateCart(mysqli $connection, int $userId): array
{
    $cart = getCartByUserId($connection, $userId);

    if ($cart) {
        return $cart;
    }

    return createCart($connection, $userId);
}

function addToCart(mysqli $connection, int $userId, int $productId): array
{
    $productResult = dbPrepareAndExecute(
        $connection,
        'SELECT stock FROM products WHERE id = ? AND active = true LIMIT 1',
        [
            ['type' => 'i', 'value' => $productId]
        ]
    );

    if (mysqli_num_rows($productResult) === 0) {
        return ['success' => false, 'error' => 'Produto não encontrado ou indisponível'];
    }

    $productStock = (int)mysqli_fetch_assoc($productResult)['stock'];

    $cart = getOrCreateCart($connection, $userId);

    $result = dbPrepareAndExecute(
        $connection,
        'SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ? LIMIT 1',
        [
            ['type' => 'i', 'value' => $cart['id']],
            ['type' => 'i', 'value' => $productId]
        ]
    );

    if (mysqli_num_rows($result) > 0) {
        $item = mysqli_fetch_assoc($result);

        if ((int)$item['quantity'] + 1 > $productStock) {
            return ['success' => false, 'error' => 'Estoque insuficiente'];
        }

        $result = dbPrepareAndExecute(
            $connection,
            'UPDATE cart_items SET quantity = quantity + 1 WHERE cart_id = ? AND product_id = ?',
            [
                ['type' => 'i', 'value' => $cart['id']],
                ['type' => 'i', 'value' => $productId]
            ]
        );

        if ($result === false || $connection->affected_rows < 1) {
            return ['success' => false, 'error' => 'Erro ao adicionar item ao carrinho'];
        }

        return ['success' => true, 'action' => 'incremented'];
    }

    if ($productStock < 1) {
        return ['success' => false, 'error' => 'Estoque insuficiente'];
    }

    $result = dbPrepareAndExecute(
        $connection,
        'INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, 1)',
        [
            ['type' => 'i', 'value' => $cart['id']],
            ['type' => 'i', 'value' => $productId]
        ]
    );

    if ($result === false || $connection->affected_rows < 1) {
        return ['success' => false, 'error' => 'Erro ao adicionar item ao carrinho'];
    }

    return ['success' => true, 'action' => 'added'];
}

function updateCartItemQuantity(mysqli $connection, int $userId, int $productId, string $action): array
{
    $cart = getCartByUserId($connection, $userId);

    if (!$cart) {
        return ['success' => false, 'error' => 'Carrinho não encontrado'];
    }

    $result = dbPrepareAndExecute(
        $connection,
        'SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ? LIMIT 1',
        [
            ['type' => 'i', 'value' => $cart['id']],
            ['type' => 'i', 'value' => $productId]
        ]
    );

    if (mysqli_num_rows($result) === 0) {
        return ['success' => false, 'error' => 'Item não encontrado'];
    }

    $item = mysqli_fetch_assoc($result);

    if ($action === 'increase') {
        $productResult = dbPrepareAndExecute(
            $connection,
            'SELECT stock FROM products WHERE id = ? AND active = true LIMIT 1',
            [
                ['type' => 'i', 'value' => $productId]
            ]
        );

        if (mysqli_num_rows($productResult) === 0) {
            return ['success' => false, 'error' => 'Produto não encontrado ou indisponível'];
        }

        $productStock = (int)mysqli_fetch_assoc($productResult)['stock'];

        if ((int)$item['quantity'] + 1 > $productStock) {
            return ['success' => false, 'error' => 'Estoque insuficiente'];
        }

        $result = dbPrepareAndExecute(
            $connection,
            'UPDATE cart_items SET quantity = quantity + 1 WHERE id = ?',
            [
                ['type' => 'i', 'value' => $item['id']]
            ]
        );

        if ($result === false || $connection->affected_rows < 1) {
            return ['success' => false, 'error' => 'Erro ao atualizar o carrinho'];
        }

        return ['success' => true, 'action' => 'incremented'];
    }

    if ($action === 'decrease') {
        if ((int)$item['quantity'] <= 1) {
            $result = dbPrepareAndExecute(
                $connection,
                'DELETE FROM cart_items WHERE id = ?',
                [
                    ['type' => 'i', 'value' => $item['id']]
                ]
            );

            if ($result === false || $connection->affected_rows < 1) {
                return ['success' => false, 'error' => 'Erro ao remover item do carrinho'];
            }

            return ['success' => true, 'action' => 'removed'];
        }

        $result = dbPrepareAndExecute(
            $connection,
            'UPDATE cart_items SET quantity = quantity - 1 WHERE id = ?',
            [
                ['type' => 'i', 'value' => $item['id']]
            ]
        );

        if ($result === false || $connection->affected_rows < 1) {
            return ['success' => false, 'error' => 'Erro ao atualizar o carrinho'];
        }

        return ['success' => true, 'action' => 'decremented'];
    }

    return ['success' => false, 'error' => 'Ação inválida'];
}

function removeCartItem(mysqli $connection, int $userId, int $productId): array
{
    $cart = getCartByUserId($connection, $userId);

    if (!$cart) {
        return ['success' => false, 'error' => 'Carrinho não encontrado'];
    }

    $result = dbPrepareAndExecute(
        $connection,
        'DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?',
        [
            ['type' => 'i', 'value' => $cart['id']],
            ['type' => 'i', 'value' => $productId]
        ]
    );

    if ($result === false || $connection->affected_rows < 1) {
        return ['success' => false, 'error' => 'Erro ao remover item do carrinho'];
    }

    return ['success' => true];
}

/**
 * @return CartItem[]
 */
function getCartItems(mysqli $connection, int $cartId): array
{
    $result = dbPrepareAndExecute(
        $connection,
        'SELECT ci.*, p.name, p.price, p.image, p.stock, p.description_line
         FROM cart_items ci
         INNER JOIN products p ON ci.product_id = p.id AND p.active = true
         WHERE ci.cart_id = ?',
        [
            ['type' => 'i', 'value' => $cartId]
        ]
    );

    if (mysqli_num_rows($result) === 0) {
        return [];
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function calculateCartTotal(array $items): int
{
    $total = 0;

    foreach ($items as $item) {
        $total += (int)$item['price'] * (int)$item['quantity'];
    }

    return $total;
}

// ─── Cookie functions (guest users) ───────────────────────────────────

function getCartItemsFromCookie(): array
{
    if (empty($_COOKIE['cart_items'])) {
        return [];
    }

    $pairs = explode(',', $_COOKIE['cart_items']);
    $items = [];

    foreach ($pairs as $pair) {
        $parts = explode(':', $pair);

        if (count($parts) !== 2) {
            continue;
        }

        $productId = (int)$parts[0];
        $quantity = (int)$parts[1];

        if ($productId < 1 || $quantity < 1) {
            continue;
        }

        $items[] = [
            'product_id' => $productId,
            'quantity' => $quantity,
        ];
    }

    return $items;
}

function saveCartCookie(array $items): void
{
    $parts = [];

    foreach ($items as $item) {
        $parts[] = (int)$item['product_id'] . ':' . (int)$item['quantity'];
    }

    setcookie('cart_items', implode(',', $parts), time() + 86400 * 30, '/');
}

function addToCartCookie(mysqli $connection, int $productId): array
{
    $result = dbPrepareAndExecute(
        $connection,
        'SELECT stock FROM products WHERE id = ? AND active = true LIMIT 1',
        [
            ['type' => 'i', 'value' => $productId]
        ]
    );

    if (mysqli_num_rows($result) === 0) {
        return ['success' => false];
    }

    $productStock = (int)mysqli_fetch_assoc($result)['stock'];

    $items = getCartItemsFromCookie();
    $found = false;

    foreach ($items as &$item) {
        if ((int)$item['product_id'] === $productId) {
            if ((int)$item['quantity'] + 1 > $productStock) {
                return ['success' => false];
            }

            $item['quantity']++;
            $found = true;
            break;
        }
    }
    unset($item);

    if (!$found) {
        if ($productStock < 1) {
            return ['success' => false];
        }

        $items[] = ['product_id' => $productId, 'quantity' => 1];
    }

    saveCartCookie($items);

    return ['success' => true, 'action' => $found ? 'incremented' : 'added'];
}

function updateCartItemQuantityCookie(mysqli $connection, int $productId, string $action): array
{
    if ($action === 'increase') {
        $result = dbPrepareAndExecute(
            $connection,
            'SELECT stock FROM products WHERE id = ? AND active = true LIMIT 1',
            [
                ['type' => 'i', 'value' => $productId]
            ]
        );

        if (mysqli_num_rows($result) === 0) {
            return ['success' => false];
        }

        $productStock = (int)mysqli_fetch_assoc($result)['stock'];
    }

    $items = getCartItemsFromCookie();

    foreach ($items as $key => $item) {
        if ((int)$item['product_id'] === $productId) {
            if ($action === 'increase') {
                if ((int)$item['quantity'] + 1 > $productStock) {
                    return ['success' => false];
                }

                $items[$key]['quantity']++;
            } elseif ($action === 'decrease') {
                if ($items[$key]['quantity'] <= 1) {
                    unset($items[$key]);
                    $items = array_values($items);
                } else {
                    $items[$key]['quantity']--;
                }
            }
            break;
        }
    }

    saveCartCookie($items);

    return ['success' => true];
}

function removeCartItemCookie(int $productId): array
{
    $items = getCartItemsFromCookie();

    foreach ($items as $key => $item) {
        if ((int)$item['product_id'] === $productId) {
            unset($items[$key]);
            $items = array_values($items);
            break;
        }
    }

    saveCartCookie($items);

    return ['success' => true];
}

/**
 * @return CartItem[]
 */
function enrichCartItemsWithProductData(mysqli $connection, array $cookieItems): array
{
    $items = [];

    foreach ($cookieItems as $cookieItem) {
        $productId = (int)$cookieItem['product_id'];

        $result = dbPrepareAndExecute(
            $connection,
            'SELECT id, name, price, image, stock, description_line FROM products WHERE id = ? AND active = true LIMIT 1',
            [
                ['type' => 'i', 'value' => $productId]
            ]
        );

        if (mysqli_num_rows($result) === 0) {
            continue;
        }

        $product = mysqli_fetch_assoc($result);

        $items[] = [
            'id' => 0,
            'cart_id' => 0,
            'product_id' => (int)$product['id'],
            'quantity' => (int)$cookieItem['quantity'],
            'name' => $product['name'],
            'price' => (int)$product['price'],
            'image' => $product['image'],
            'stock' => (int)$product['stock'],
            'description_line' => $product['description_line'],
            'created_at' => '',
            'updated_at' => '',
        ];
    }

    return $items;
}
