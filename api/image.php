<?php
require_once __DIR__ . '/../includes/db.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Respond to CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get product image
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT Obrazok, mime_type FROM produkty WHERE ID = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        if ($product && $product['Obrazok']) {
            $mime = $product['mime_type'] ?: 'image/jpeg';
            header('Content-Type: ' . $mime);
            header('Content-Length: ' . strlen($product['Obrazok']));
            header('Cache-Control: public, max-age=86400'); // Cache for 1 day
            echo $product['Obrazok'];
        } else {
            // Return placeholder image URL or 404
            header('HTTP/1.1 404 Not Found');
            echo 'Image not found';
        }
    } catch (PDOException $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo 'Error: ' . $e->getMessage();
    }
    exit;
}

// Upload product image
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Accept id from POST, GET or REQUEST for robustness
    $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : (int)($_GET['id'] ?? 0));

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing product id']);
        exit;
    }

    if (!isset($_FILES['image']) || !is_array($_FILES['image'])) {
        http_response_code(400);
        echo json_encode([
            'error' => 'No image uploaded',
            'details' => [
                'files_present' => isset($_FILES) ? array_keys($_FILES) : [],
                'post_size' => ini_get('post_max_size')
            ]
        ]);
        exit;
    }

    $file = $_FILES['image'];

    // 1. Check for PHP upload errors FIRST
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessage = 'File upload failed.';
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errorMessage = 'File is too large (exceeds server limits).';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errorMessage = 'File was only partially uploaded.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errorMessage = 'No file was uploaded.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $errorMessage = 'Missing a temporary folder.';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $errorMessage = 'Failed to write file to disk.';
                break;
            case UPLOAD_ERR_EXTENSION:
                $errorMessage = 'File upload stopped by extension.';
                break;
            default:
                $errorMessage = 'Unknown upload error code: ' . $file['error'];
        }
        
        http_response_code(400);
        echo json_encode(['error' => $errorMessage]);
        exit;
    }

    // 2. Validate tmp_name before using it
    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid upload (empty tmp_name).']);
        exit;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    // Now it is safe to call mime_content_type
    $mimeType = mime_content_type($file['tmp_name']);
    
    if (!in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.']);
        exit;
    }
    
    try {
        $imageData = file_get_contents($file['tmp_name']);

        $db = getDB();
        $stmt = $db->prepare("UPDATE produkty SET Obrazok = ?, mime_type = ? WHERE ID = ?");
        // Bind as LOB to be safe
        $stmt->bindValue(1, $imageData, PDO::PARAM_LOB);
        $stmt->bindValue(2, $mimeType);
        $stmt->bindValue(3, $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'id' => $id
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid request']);