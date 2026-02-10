<?php 
require_once 'includes/header.php';
require_once 'includes/db.php';

$products = getAllProducts();
?>

<div id="products-section">
    <div class="products-header visible">
        <h1>FENEX</h1>
    </div>
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Hľadaj produkt...">
    </div>
    <div class="products" id="produkty">
        <?php foreach ($products as $product): ?>
            <div class="product visible" 
                 data-id="<?php echo $product['ID']; ?>"
                 data-name="<?php echo htmlspecialchars($product['Meno']); ?>"
                 data-price="<?php echo $product['Cena']; ?>"
                 data-description="<?php echo htmlspecialchars($product['Popis']); ?>">
                <?php if ($product['Obrazok']): ?>
                    <img src="api/image.php?id=<?php echo $product['ID']; ?>" 
                         alt="<?php echo htmlspecialchars($product['Meno']); ?>"
                         loading="lazy">
                <?php else: ?>
                    <div class="product-placeholder">
                        <span><?php echo htmlspecialchars($product['Meno']); ?></span>
                    </div>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($product['Meno']); ?></h3>
                <p><?php echo htmlspecialchars(substr($product['Popis'], 0, 80)); ?>...</p>
                <div class="price"><?php echo number_format($product['Cena'], 2); ?> €</div>
                <button class="add-to-cart-btn">Pridať do košíka</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
