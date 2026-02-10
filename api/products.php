<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$response = ['success' => false];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';
    
    switch ($action) {
        case 'list':
            $products = getAllProducts();
            $response = ['success' => true, 'products' => $products];
            break;
            
        case 'search':
            $query = $_GET['q'] ?? '';
            if (empty($query)) {
                $products = getAllProducts();
            } else {
                $products = searchProducts($query);
            }
            $response = ['success' => true, 'products' => $products];
            break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            $product = getProductById($id);
            if ($product) {
                $response = ['success' => true, 'product' => $product];
            } else {
                $response = ['success' => false, 'message' => 'Produkt nenájdený'];
            }
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Neplatná akcia'];
    }
} else {
    $response = ['success' => false, 'message' => 'Neplatná metóda'];
}

echo json_encode($response);
