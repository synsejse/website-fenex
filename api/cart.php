<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/cart.php';

$method = $_SERVER['REQUEST_METHOD'];
$response = ['success' => false];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $name = $data['name'] ?? '';
            $price = floatval($data['price'] ?? 0);
            $qty = intval($data['qty'] ?? 1);
            $size = $data['size'] ?? null;
            $color = $data['color'] ?? null;
            
            if (empty($name) || $price <= 0) {
                $response = ['success' => false, 'message' => 'Neplatné dáta'];
            } else {
                $response = addToCart($name, $price, $qty, $size, $color);
                $response['cart'] = getCart();
                $response['count'] = getCartCount();
                $response['total'] = getCartTotal();
            }
            break;
            
        case 'remove':
            $index = intval($data['index'] ?? -1);
            $response = removeFromCart($index);
            $response['cart'] = getCart();
            $response['count'] = getCartCount();
            $response['total'] = getCartTotal();
            break;
            
        case 'update':
            $index = intval($data['index'] ?? -1);
            $qty = intval($data['qty'] ?? 1);
            $response = updateCartQuantity($index, $qty);
            $response['cart'] = getCart();
            $response['count'] = getCartCount();
            $response['total'] = getCartTotal();
            break;
            
        case 'clear':
            $response = clearCart();
            $response['cart'] = [];
            $response['count'] = 0;
            $response['total'] = 0;
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Neplatná akcia'];
    }
} else if ($method === 'GET') {
    $response = [
        'success' => true,
        'cart' => getCart(),
        'count' => getCartCount(),
        'total' => getCartTotal()
    ];
} else {
    $response = ['success' => false, 'message' => 'Neplatná metóda'];
}

echo json_encode($response);
