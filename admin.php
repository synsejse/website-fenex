<?php 
require_once 'includes/header.php';
require_once 'includes/auth.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: index.php');
    exit;
}
?>

<div class="admin-container">
    <h1>Admin Panel</h1>
    
    <div class="admin-tabs">
        <button class="tab-btn active" data-tab="products">Produkty</button>
        <button class="tab-btn" data-tab="orders">Objednávky</button>
        <button class="tab-btn" data-tab="contacts">Kontakty</button>
        <button class="tab-btn" data-tab="users">Používatelia</button>
    </div>

    <!-- Products Tab -->
    <div id="products-tab" class="tab-content active">
        <h2>Správa produktov</h2>
        <div class="admin-products-list" id="admin-products"></div>
    </div>

    <!-- Orders Tab -->
    <div id="orders-tab" class="tab-content">
        <h2>Objednávky</h2>
        <div class="admin-orders-list" id="admin-orders"></div>
    </div>

    <!-- Contacts Tab -->
    <div id="contacts-tab" class="tab-content">
        <h2>Kontaktné správy</h2>
        <div class="admin-contacts-list" id="admin-contacts"></div>
    </div>

    <!-- Users Tab -->
    <div id="users-tab" class="tab-content">
        <h2>Používatelia</h2>
        <div class="admin-users-list" id="admin-users"></div>
    </div>
</div>

<!-- Product Edit Modal -->
<div id="edit-product-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Upraviť produkt</h2>
        <form id="edit-product-form" enctype="multipart/form-data">
            <input type="hidden" id="edit-product-id">
            <input type="text" id="edit-product-name" placeholder="Názov produktu" required>
            <textarea id="edit-product-description" placeholder="Popis" rows="4" required></textarea>
            <input type="number" id="edit-product-price" placeholder="Cena" step="0.01" required>
            <input type="file" id="edit-product-image" accept="image/*">
            <div id="current-image-preview"></div>
            <button type="submit" class="btn">Uložiť</button>
        </form>
    </div>
</div>

<style>
.admin-container {
    max-width: 1400px;
    margin: 20px auto;
    padding: 20px;
}

.admin-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid #333;
}

.tab-btn {
    background: transparent;
    color: #eee;
    border: none;
    padding: 15px 30px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    transition: 0.3s;
    border-bottom: 3px solid transparent;
}

.tab-btn:hover {
    background: rgba(255, 255, 255, 0.05);
}

.tab-btn.active {
    border-bottom-color: #eee;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.admin-products-list, .admin-orders-list, .admin-contacts-list, .admin-users-list {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 20px;
    min-height: 400px;
}

.admin-product-item, .admin-order-item, .admin-contact-item, .admin-user-item {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-product-item img {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
    margin-right: 15px;
}

.admin-product-info {
    flex: 1;
}

.admin-actions {
    display: flex;
    gap: 10px;
}

.admin-actions button {
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: 0.3s;
}

.edit-btn {
    background: #4CAF50;
    color: white;
}

.edit-btn:hover {
    background: #45a049;
}

.delete-btn {
    background: #f44336;
    color: white;
}

.delete-btn:hover {
    background: #da190b;
}

.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: #1f1f1f;
    padding: 30px;
    border-radius: 15px;
    width: 90%;
    max-width: 600px;
    position: relative;
}

.modal-content .close {
    position: absolute;
    right: 20px;
    top: 15px;
    font-size: 30px;
    cursor: pointer;
    color: #eee;
}

.modal-content input, .modal-content textarea {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #333;
    border-radius: 8px;
    background: #2a2a2a;
    color: #eee;
    font-size: 14px;
}

#current-image-preview img {
    max-width: 200px;
    margin: 10px 0;
    border-radius: 8px;
}

.order-items {
    margin-top: 10px;
    font-size: 14px;
    color: #ccc;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-new {
    background: #4CAF50;
    color: white;
}

.status-read {
    background: #2196F3;
    color: white;
}

.status-replied {
    background: #9E9E9E;
    color: white;
}
</style>

<script src="assets/js/admin.js"></script>

<?php require_once 'includes/footer.php'; ?>
