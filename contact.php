<?php require_once 'includes/header.php'; ?>

<div id="contact-section">
    <h2>Kontaktujte nás</h2>
    <form id="contact-form">
        <input type="text" id="contact-name" placeholder="Meno" required>
        <input type="email" id="contact-email" placeholder="Email" required>
        <textarea id="contact-message" rows="4" placeholder="Vaša správa" required></textarea>
        <button type="submit" class="btn">Odoslať</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
