<?php
require_once __DIR__ . '/../config.php';

// Database connection
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

// Get all products
function getAllProducts() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM produkty ORDER BY ID");
    return $stmt->fetchAll();
}

// Get product by ID
function getProductById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM produkty WHERE ID = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Search products
function searchProducts($query) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM produkty WHERE Meno LIKE ? OR Popis LIKE ?");
    $searchTerm = '%' . $query . '%';
    $stmt->execute([$searchTerm, $searchTerm]);
    return $stmt->fetchAll();
}

// Create order
function createOrder($userId, $items, $totalPrice, $customerName, $email, $address, $note = '') {
    $db = getDB();
    
    try {
        $db->beginTransaction();
        
        // Insert order
        $stmt = $db->prepare("
            INSERT INTO objednavky (user_id, customer_name, email, address, note, total_price, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $customerName, $email, $address, $note, $totalPrice]);
        $orderId = $db->lastInsertId();
        
        // Insert order items
        $stmt = $db->prepare("
            INSERT INTO order_items (order_id, product_name, price, quantity, subtotal) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($items as $item) {
            $stmt->execute([
                $orderId,
                $item['name'],
                $item['price'],
                $item['qty'],
                $item['price'] * $item['qty']
            ]);
        }
        
        $db->commit();
        return $orderId;
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

// Get user orders
function getUserOrders($userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM objednavky WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Get order items
function getOrderItems($orderId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll();
}
