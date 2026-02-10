<?php
require_once __DIR__ . '/../config.php';

// Initialize cart in session
function initCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

// Add item to cart
function addToCart($name, $price, $qty = 1, $size = null, $color = null) {
    initCart();
    
    $itemName = $name;
    if ($size && $color) {
        $itemName = "$name ($size, $color)";
    }
    
    // Check if item already exists
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['name'] === $itemName) {
            $item['qty'] += $qty;
            $found = true;
            break;
        }
    }
    
    // Add new item if not found
    if (!$found) {
        $_SESSION['cart'][] = [
            'name' => $itemName,
            'price' => $price,
            'qty' => $qty
        ];
    }
    
    return ['success' => true, 'message' => "$itemName bol pridaný do košíka!"];
}

// Remove item from cart
function removeFromCart($index) {
    initCart();
    
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
        return ['success' => true, 'message' => 'Položka bola odstránená z košíka'];
    }
    
    return ['success' => false, 'message' => 'Položka nebola nájdená'];
}

// Update cart item quantity
function updateCartQuantity($index, $qty) {
    initCart();
    
    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['qty'] = max(1, (int)$qty);
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Položka nebola nájdená'];
}

// Get cart items
function getCart() {
    initCart();
    return $_SESSION['cart'];
}

// Get cart total
function getCartTotal() {
    initCart();
    $total = 0;
    
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['qty'];
    }
    
    return $total;
}

// Get cart item count
function getCartCount() {
    initCart();
    $count = 0;
    
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['qty'];
    }
    
    return $count;
}

// Clear cart
function clearCart() {
    $_SESSION['cart'] = [];
    return ['success' => true, 'message' => 'Košík bol vyprázdnený'];
}
