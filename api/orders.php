<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart.php';
require_once __DIR__ . '/../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$response = ['success' => false];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $customerName = $data['name'] ?? '';
            $email = $data['email'] ?? '';
            $address = $data['address'] ?? '';
            $note = $data['note'] ?? '';
            
            if (empty($customerName) || empty($email) || empty($address)) {
                $response = ['success' => false, 'message' => 'Meno, email a adresa sú povinné'];
                break;
            }
            
            $cart = getCart();
            if (empty($cart)) {
                $response = ['success' => false, 'message' => 'Košík je prázdny'];
                break;
            }
            
            $total = getCartTotal();
            $userId = getCurrentUser()['id'] ?? null;
            
            try {
                $orderId = createOrder($userId, $cart, $total, $customerName, $email, $address, $note);
                clearCart();
                $response = [
                    'success' => true, 
                    'message' => 'Objednávka bola úspešne odoslaná! Ďakujeme.',
                    'order_id' => $orderId
                ];
            } catch (Exception $e) {
                $response = ['success' => false, 'message' => 'Chyba pri vytváraní objednávky: ' . $e->getMessage()];
            }
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Neplatná akcia'];
    }
} else if ($method === 'GET') {
    if (!isLoggedIn()) {
        $response = ['success' => false, 'message' => 'Musíte byť prihlásený'];
    } else {
        $user = getCurrentUser();
        $orders = getUserOrders($user['id']);
        $response = ['success' => true, 'orders' => $orders];
    }
} else {
    $response = ['success' => false, 'message' => 'Neplatná metóda'];
}

echo json_encode($response);
