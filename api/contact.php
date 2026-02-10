<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$response = ['success' => false];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $message = trim($data['message'] ?? '');
    
    // Validation
    if (empty($name)) {
        $response['error'] = 'Meno je povinné';
        echo json_encode($response);
        exit;
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['error'] = 'Platný email je povinný';
        echo json_encode($response);
        exit;
    }
    
    if (empty($message)) {
        $response['error'] = 'Správa je povinná';
        echo json_encode($response);
        exit;
    }
    
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $message]);
        
        $response['success'] = true;
        $response['message'] = 'Vaša správa bola úspešne odoslaná!';
    } catch (PDOException $e) {
        $response['error'] = 'Chyba pri odosielaní správy. Skúste to prosím neskôr.';
        error_log('Contact form error: ' . $e->getMessage());
    }
} else {
    $response['error'] = 'Neplatná metóda požiadavky';
}

echo json_encode($response);
