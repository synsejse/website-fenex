<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db.php';

// Register a new user
function registerUser($name, $email, $password) {
    $db = getDB();
    
    // Check if user already exists
    $stmt = $db->prepare("SELECT ID FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Používateľ s týmto emailom už existuje!'];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    try {
        $stmt = $db->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $hashedPassword]);
        
        return ['success' => true, 'message' => 'Registrácia úspešná!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Chyba pri registrácii: ' . $e->getMessage()];
    }
}

// Login user
function loginUser($email, $password) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Nesprávny email alebo heslo!'];
    }
    
    // Set session
    $_SESSION['user_id'] = $user['ID'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['is_admin'] = (bool)$user['is_admin'];
    
    return ['success' => true, 'message' => 'Prihlásenie úspešné!', 'user' => [
        'id' => $user['ID'],
        'name' => $user['name'],
        'email' => $user['email'],
        'is_admin' => (bool)$user['is_admin']
    ]];
}

// Logout user
function logoutUser() {
    session_unset();
    session_destroy();
    return ['success' => true, 'message' => 'Odhlásený'];
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email']
    ];
}

// Get user by ID
function getUserById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT ID, name, email, created_at FROM users WHERE ID = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}
