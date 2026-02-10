<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$response = ['success' => false];

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    
    try {
        $db = getDB();
        
        switch ($action) {
            case 'products':
                $stmt = $db->query("SELECT ID, Meno, Popis, Cena, IF(Obrazok IS NOT NULL AND LENGTH(Obrazok) > 0, 1, 0) as Obrazok FROM produkty ORDER BY ID");
                $response['products'] = $stmt->fetchAll();
                $response['success'] = true;
                break;
                
            case 'product':
                $id = (int)($_GET['id'] ?? 0);
                $stmt = $db->prepare("SELECT * FROM produkty WHERE ID = ?");
                $stmt->execute([$id]);
                $response['product'] = $stmt->fetch();
                $response['success'] = true;
                break;
                
            case 'orders':
                $stmt = $db->query("SELECT * FROM objednavky ORDER BY created_at DESC");
                $response['orders'] = $stmt->fetchAll();
                $response['success'] = true;
                break;
                
            case 'order_items':
                $id = (int)($_GET['id'] ?? 0);
                $stmt = $db->prepare("
                    SELECT * FROM order_items WHERE order_id = ?
                ");
                $stmt->execute([$id]);
                $response['items'] = $stmt->fetchAll();
                $response['success'] = true;
                break;
                
            case 'contacts':
                $stmt = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
                $response['contacts'] = $stmt->fetchAll();
                $response['success'] = true;
                break;
                
            case 'users':
                $stmt = $db->query("SELECT ID, name, email, created_at, is_admin FROM users ORDER BY created_at DESC");
                $response['users'] = $stmt->fetchAll();
                $response['success'] = true;
                break;
                
            case 'user':
                $id = (int)($_GET['id'] ?? 0);
                $stmt = $db->prepare("SELECT ID, name, email, created_at, is_admin FROM users WHERE ID = ?");
                $stmt->execute([$id]);
                $response['user'] = $stmt->fetch();
                $response['success'] = true;
                break;
                
            default:
                $response['error'] = 'Invalid action';
        }
    } catch (PDOException $e) {
        $response['error'] = 'Database error: ' . $e->getMessage();
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    try {
        $db = getDB();
        
        switch ($action) {
            case 'update_product':
                $id = (int)($data['id'] ?? 0);
                $name = trim($data['name'] ?? '');
                $description = trim($data['description'] ?? '');
                $price = (float)($data['price'] ?? 0);
                
                if (empty($name) || $price <= 0) {
                    $response['error'] = 'Invalid product data';
                    break;
                }
                
                $stmt = $db->prepare("UPDATE produkty SET Meno = ?, Popis = ?, Cena = ? WHERE ID = ?");
                $stmt->execute([$name, $description, $price, $id]);
                
                $response['success'] = true;
                $response['message'] = 'Product updated successfully';
                break;
                
            case 'mark_contact_read':
                $id = (int)($data['id'] ?? 0);
                $stmt = $db->prepare("UPDATE contact_messages SET status = 'read' WHERE ID = ?");
                $stmt->execute([$id]);
                
                $response['success'] = true;
                $response['message'] = 'Contact marked as read';
                break;
                
            case 'update_order_status':
                $id = (int)($data['id'] ?? 0);
                $status = trim($data['status'] ?? '');
                $allowedStatuses = ['new', 'processing', 'shipped', 'delivered', 'cancelled'];
                
                if (!in_array($status, $allowedStatuses)) {
                    $response['error'] = 'Invalid status';
                    break;
                }
                
                $stmt = $db->prepare("UPDATE objednavky SET status = ? WHERE ID = ?");
                $stmt->execute([$status, $id]);
                
                $response['success'] = true;
                $response['message'] = 'Order status updated';
                break;
                
            case 'delete_order':
                $id = (int)($data['id'] ?? 0);
                
                // Delete order items first
                $stmt = $db->prepare("DELETE FROM order_items WHERE order_id = ?");
                $stmt->execute([$id]);
                
                // Delete order
                $stmt = $db->prepare("DELETE FROM objednavky WHERE ID = ?");
                $stmt->execute([$id]);
                
                $response['success'] = true;
                $response['message'] = 'Order deleted';
                break;
                
            case 'toggle_admin':
                $id = (int)($data['id'] ?? 0);
                $isAdmin = (int)($data['is_admin'] ?? 0);
                
                $stmt = $db->prepare("UPDATE users SET is_admin = ? WHERE ID = ?");
                $stmt->execute([$isAdmin, $id]);
                
                $response['success'] = true;
                $response['message'] = 'Admin status updated';
                break;
                
            case 'update_user':
                $id = (int)($data['id'] ?? 0);
                $name = trim($data['name'] ?? '');
                $email = trim($data['email'] ?? '');
                
                if (empty($name) || empty($email)) {
                    $response['error'] = 'Invalid user data';
                    break;
                }
                
                // Check if email exists for another user
                $stmt = $db->prepare("SELECT ID FROM users WHERE email = ? AND ID != ?");
                $stmt->execute([$email, $id]);
                if ($stmt->fetch()) {
                    $response['error'] = 'Email already exists';
                    break;
                }
                
                $stmt = $db->prepare("UPDATE users SET name = ?, email = ? WHERE ID = ?");
                $stmt->execute([$name, $email, $id]);
                
                $response['success'] = true;
                $response['message'] = 'User updated';
                break;
                
            case 'delete_user':
                $id = (int)($data['id'] ?? 0);
                
                $stmt = $db->prepare("DELETE FROM users WHERE ID = ?");
                $stmt->execute([$id]);
                
                $response['success'] = true;
                $response['message'] = 'User deleted';
                break;
                
            default:
                $response['error'] = 'Invalid action';
        }
    } catch (PDOException $e) {
        $response['error'] = 'Database error: ' . $e->getMessage();
    }
}

echo json_encode($response);
