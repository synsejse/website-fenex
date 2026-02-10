<?php
// Database configuration
define('DB_HOST', getenv('DB_HOSTNAME') ?: 'mariadb');
define('DB_USER', getenv('DB_USER') ?: 'root');
// Support both DB_PASS and DB_PASSWORD environment variable names (compose uses DB_PASSWORD)
define('DB_PASS', getenv('DB_PASSWORD') ?: 'rootpassword');
define('DB_NAME', getenv('DB_NAME') ?: 'fenex');

// Site configuration
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost:8080');
define('SITE_NAME', 'FENEX');

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Europe/Bratislava');
