<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart.php';

$currentUser = getCurrentUser();
$cartCount = getCartCount();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <h1><?php echo SITE_NAME; ?></h1>
    <nav>
        <a href="index.php" class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>">Domov</a>
        <a href="products.php" class="nav-link <?php echo $currentPage === 'products' ? 'active' : ''; ?>">Produkty</a>
        <a href="about.php" class="nav-link <?php echo $currentPage === 'about' ? 'active' : ''; ?>">O nás</a>
        <a href="contact.php" class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>">Kontakt</a>
    </nav>
    
    <div class="header-right">
        <?php if ($currentUser): ?>
            <span id="userInfo" style="color:#eee; font-weight:600;">👤 <?php echo htmlspecialchars($currentUser['name']); ?></span>
            <?php if (isAdmin()): ?>
                <a href="admin.php" class="auth-btn" style="background:#4CAF50;text-decoration:none;display:inline-block;line-height:normal;">Admin Panel</a>
            <?php endif; ?>
            <button class="auth-btn" id="logoutBtn">Odhlásiť</button>
        <?php else: ?>
            <button class="auth-btn" id="loginBtn">Prihlásiť</button>
            <button class="auth-btn" id="registerBtn">Registrovať</button>
        <?php endif; ?>
        <a href="cart.php" id="cart">🛒 <span id="cart-count"><?php echo $cartCount; ?></span></a>
    </div>
</header>

<div id="login-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" id="login-close">&times;</span>
        <h2>Prihlásenie</h2>
        <form id="login-form">
            <input type="email" id="login-email" placeholder="Email" required>
            <input type="password" id="login-password" placeholder="Heslo" required>
            <button type="submit" class="btn">Prihlásiť sa</button>
        </form>
    </div>
</div>

<div id="register-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" id="register-close">&times;</span>
        <h2>Registrácia</h2>
        <form id="register-form">
            <input type="text" id="reg-name" placeholder="Meno" required>
            <input type="email" id="reg-email" placeholder="Email" required>
            <input type="password" id="reg-password" placeholder="Heslo" required>
            <button type="submit" class="btn">Registrovať</button>
        </form>
    </div>
</div>

<div id="product-modal" class="modal" style="display:none;">
    <div class="modal-content product-modal-content">
        <span class="close" id="modal-close">&times;</span>
        <img id="modal-img" src="" alt="">
        <h2 id="modal-name"></h2>
        <p id="modal-description"></p>
        <div class="modal-price" id="modal-price"></div>
        <div class="modal-option">
            <label>Veľkosť:</label>
            <select id="modal-size">
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
            </select>
        </div>
        <div class="modal-option">
            <label>Farba:</label>
            <select id="modal-color">
                <option value="Čierna">Čierna</option>
                <option value="Biela">Biela</option>
                <option value="Červená">Červená</option>
                <option value="Modrá">Modrá</option>
            </select>
        </div>
        <div class="modal-option">
            <label>Množstvo:</label>
            <input type="number" id="modal-qty" value="1" min="1">
        </div>
        <button id="modal-add" class="btn">Pridať do košíka</button>
    </div>
</div>

<div class="container">
