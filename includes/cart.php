<?php

declare(strict_types=1);

require_once __DIR__ . '/init.php';

function cart_init(): void
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [
            'washers' => [],
            'parts' => [],
            'additions' => [],
        ];
    }
}

function cart_add(string $type, int $id, int $qty = 1): void
{
    cart_init();

    if (!isset($_SESSION['cart'][$type][$id])) {
        $_SESSION['cart'][$type][$id] = 0;
    }

    $_SESSION['cart'][$type][$id] += max(1, $qty);
}

function cart_remove(string $type, int $id, int $qty = 1): void
{
    cart_init();

    if (!isset($_SESSION['cart'][$type][$id])) {
        return;
    }

    $_SESSION['cart'][$type][$id] -= max(1, $qty);

    if ($_SESSION['cart'][$type][$id] <= 0) {
        unset($_SESSION['cart'][$type][$id]);
    }
}

function cart_clear(): void
{
    $_SESSION['cart'] = [
        'washers' => [],
        'parts' => [],
        'additions' => [],
    ];
}
