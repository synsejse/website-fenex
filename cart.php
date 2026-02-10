<?php 
require_once 'includes/header.php';
require_once 'includes/cart.php';

$cart = getCart();
$total = getCartTotal();
?>

<div id="cart-section">
    <h2>🛒 Tvoj košík</h2>
    
    <?php if (empty($cart)): ?>
        <p style="text-align:center; padding:40px; color:#ccc;">Košík je prázdny</p>
        <div style="text-align:center;">
            <a href="products.php" class="btn">Pokračovať v nákupe</a>
        </div>
    <?php else: ?>
        <table id="cart-table">
            <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Cena</th>
                    <th>Množstvo</th>
                    <th>Spolu</th>
                    <th>Odstrániť</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $index => $item): ?>
                    <tr>
                        <td data-label="Produkt"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td data-label="Cena"><?php echo number_format($item['price'], 2); ?> €</td>
                        <td data-label="Množstvo">
                            <input type="number" 
                                   class="qty-input" 
                                   min="1" 
                                   value="<?php echo $item['qty']; ?>" 
                                   data-index="<?php echo $index; ?>">
                        </td>
                        <td data-label="Spolu"><?php echo number_format($item['price'] * $item['qty'], 2); ?> €</td>
                        <td data-label="Odstrániť">
                            <button class="btn remove-btn" data-index="<?php echo $index; ?>">X</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total" id="total-price">Spolu: <?php echo number_format($total, 2); ?> €</div>
        <div style="text-align:center; margin-top:20px;">
            <a href="products.php" class="btn" style="margin-right:10px;">Pokračovať v nákupe</a>
            <a href="order.php" class="btn" id="to-order">Pokračovať na objednávku</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
