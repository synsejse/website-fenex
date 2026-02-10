<?php 
require_once 'includes/header.php';
require_once 'includes/cart.php';

$cart = getCart();
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}
?>

<div id="order-section">
    <h2>📦 Objednávkový formulár</h2>
    <form id="order-form">
        <input type="text" id="order-name" placeholder="Meno a priezvisko" required>
        <input type="email" id="order-email" placeholder="Email" required>
        <input type="text" id="order-address" placeholder="Adresa doručenia" required>
        <textarea id="order-note" rows="4" placeholder="Poznámka k objednávke"></textarea>
        <button type="submit" class="btn">Odoslať objednávku</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
