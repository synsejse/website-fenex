<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$response = ['success' => false];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    switch ($action) {
        case 'register':
            $name = $data['name'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            
            if (empty($name) || empty($email) || empty($password)) {
                $response = ['success' => false, 'message' => 'Všetky polia sú povinné'];
            } else {
                $response = registerUser($name, $email, $password);
            }
            break;
            
        case 'login':
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $response = ['success' => false, 'message' => 'Email a heslo sú povinné'];
            } else {
                $response = loginUser($email, $password);
            }
            break;
            
        case 'logout':
            $response = logoutUser();
            break;
            
        case 'check':
            $user = getCurrentUser();
            if ($user) {
                $response = ['success' => true, 'user' => $user];
            } else {
                $response = ['success' => false, 'message' => 'Nie ste prihlásený'];
            }
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Neplatná akcia'];
    }
} else {
    $response = ['success' => false, 'message' => 'Neplatná metóda'];
}

echo json_encode($response);
