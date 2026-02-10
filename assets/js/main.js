// -------------------- AUTH MODALS --------------------
const loginModal = document.getElementById('login-modal');
const registerModal = document.getElementById('register-modal');
const loginBtn = document.getElementById('loginBtn');
const registerBtn = document.getElementById('registerBtn');
const logoutBtn = document.getElementById('logoutBtn');
const loginClose = document.getElementById('login-close');
const registerClose = document.getElementById('register-close');

if (loginBtn) {
    loginBtn.onclick = () => loginModal.style.display = 'flex';
}

if (registerBtn) {
    registerBtn.onclick = () => registerModal.style.display = 'flex';
}

if (loginClose) {
    loginClose.onclick = () => loginModal.style.display = 'none';
}

if (registerClose) {
    registerClose.onclick = () => registerModal.style.display = 'none';
}

window.onclick = (e) => {
    if (e.target === loginModal) loginModal.style.display = 'none';
    if (e.target === registerModal) registerModal.style.display = 'none';
};

// -------------------- REGISTER --------------------
const registerForm = document.getElementById('register-form');
if (registerForm) {
    registerForm.onsubmit = async (e) => {
        e.preventDefault();
        
        const name = document.getElementById('reg-name').value;
        const email = document.getElementById('reg-email').value;
        const password = document.getElementById('reg-password').value;
        
        const response = await fetch('api/auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'register', name, email, password })
        });
        
        const data = await response.json();
        alert(data.message);
        
        if (data.success) {
            registerModal.style.display = 'none';
            registerForm.reset();
            if (loginModal) loginModal.style.display = 'flex';
        }
    };
}

// -------------------- LOGIN --------------------
const loginForm = document.getElementById('login-form');
if (loginForm) {
    loginForm.onsubmit = async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        
        const response = await fetch('api/auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'login', email, password })
        });
        
        const data = await response.json();
        alert(data.message);
        
        if (data.success) {
            loginModal.style.display = 'none';
            loginForm.reset();
            location.reload();
        }
    };
}

// -------------------- LOGOUT --------------------
if (logoutBtn) {
    logoutBtn.onclick = async () => {
        const response = await fetch('api/auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'logout' })
        });
        
        const data = await response.json();
        alert(data.message);
        location.reload();
    };
}

// -------------------- PRODUCT MODAL --------------------
const productModal = document.getElementById('product-modal');
const modalImg = document.getElementById('modal-img');
const modalName = document.getElementById('modal-name');
const modalDescription = document.getElementById('modal-description');
const modalPrice = document.getElementById('modal-price');
const modalSize = document.getElementById('modal-size');
const modalColor = document.getElementById('modal-color');
const modalQty = document.getElementById('modal-qty');
const modalAdd = document.getElementById('modal-add');
const modalClose = document.getElementById('modal-close');

const produktyDiv = document.getElementById('produkty');

if (produktyDiv) {
    produktyDiv.addEventListener('click', (e) => {
        const product = e.target.closest('.product');
        if (!product) return;
        
        // Click on image to open modal
        if (e.target.tagName === 'IMG') {
            modalImg.src = product.querySelector('img').src;
            modalName.textContent = product.dataset.name;
            if (modalDescription) {
                modalDescription.textContent = product.dataset.description;
            }
            modalPrice.textContent = parseFloat(product.dataset.price).toFixed(2) + ' €';
            modalQty.value = 1;
            modalSize.value = 'M';
            modalColor.value = 'Čierna';
            productModal.style.display = 'flex';
        }
        
        // Click on "Add to cart" button
        if (e.target.classList.contains('add-to-cart-btn')) {
            addToCart(
                product.dataset.name,
                parseFloat(product.dataset.price),
                1
            );
        }
    });
}

if (modalClose) {
    modalClose.onclick = () => productModal.style.display = 'none';
}

window.onclick = (e) => {
    if (e.target === productModal) productModal.style.display = 'none';
};

// Add from modal
if (modalAdd) {
    modalAdd.onclick = () => {
        const name = modalName.textContent;
        const price = parseFloat(modalPrice.textContent);
        const size = modalSize.value;
        const color = modalColor.value;
        const qty = parseInt(modalQty.value) || 1;
        
        addToCart(name, price, qty, size, color);
        productModal.style.display = 'none';
    };
}

// -------------------- CART OPERATIONS --------------------
async function addToCart(name, price, qty = 1, size = null, color = null) {
    const response = await fetch('api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'add', name, price, qty, size, color })
    });
    
    const data = await response.json();
    if (data.success) {
        alert(data.message);
        updateCartCount(data.count);
    }
}

async function removeFromCart(index) {
    const response = await fetch('api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'remove', index })
    });
    
    const data = await response.json();
    if (data.success) {
        location.reload();
    }
}

async function updateCartQty(index, qty) {
    const response = await fetch('api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'update', index, qty })
    });
    
    const data = await response.json();
    if (data.success) {
        location.reload();
    }
}

function updateCartCount(count) {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = count;
    }
}

// Cart page event listeners
const cartTable = document.querySelector('#cart-table tbody');
if (cartTable) {
    cartTable.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-btn')) {
            const index = parseInt(e.target.dataset.index);
            if (confirm('Naozaj chcete odstrániť túto položku?')) {
                removeFromCart(index);
            }
        }
    });
    
    cartTable.addEventListener('change', (e) => {
        if (e.target.classList.contains('qty-input')) {
            const index = parseInt(e.target.dataset.index);
            const qty = parseInt(e.target.value) || 1;
            updateCartQty(index, qty);
        }
    });
}

// -------------------- SEARCH --------------------
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('keyup', () => {
        const filter = searchInput.value.toLowerCase();
        const products = document.querySelectorAll('.product');
        
        products.forEach(product => {
            const name = product.dataset.name.toLowerCase();
            const description = product.dataset.description ? product.dataset.description.toLowerCase() : '';
            
            if (name.includes(filter) || description.includes(filter)) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
        });
    });
}

// -------------------- ORDER FORM --------------------
const orderForm = document.getElementById('order-form');
if (orderForm) {
    orderForm.onsubmit = async (e) => {
        e.preventDefault();
        
        const name = document.getElementById('order-name').value;
        const email = document.getElementById('order-email').value;
        const address = document.getElementById('order-address').value;
        const note = document.getElementById('order-note').value;
        
        const response = await fetch('api/orders.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'create', name, email, address, note })
        });
        
        const data = await response.json();
        alert(data.message);
        
        if (data.success) {
            window.location.href = 'index.php';
        }
    };
}

// -------------------- CONTACT FORM --------------------
const contactForm = document.getElementById('contact-form');
if (contactForm) {
    contactForm.onsubmit = async (e) => {
        e.preventDefault();
        
        const name = document.getElementById('contact-name').value;
        const email = document.getElementById('contact-email').value;
        const message = document.getElementById('contact-message').value;
        
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Odosielam...';
        submitBtn.disabled = true;
        
        try {
            const response = await fetch('api/contact.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, email, message })
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert(data.message);
                contactForm.reset();
            } else {
                alert(data.error || 'Nastala chyba pri odosielaní správy.');
            }
        } catch (error) {
            alert('Nastala chyba. Skúste to prosím neskôr.');
            console.error('Contact form error:', error);
        } finally {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    };
}

// -------------------- ANIMATIONS --------------------
// Fade in products on load
window.addEventListener('load', () => {
    const products = document.querySelectorAll('.product');
    products.forEach((product, index) => {
        setTimeout(() => {
            product.classList.add('visible');
        }, index * 100);
    });
    
    const header = document.querySelector('.products-header');
    if (header) {
        setTimeout(() => {
            header.classList.add('visible');
        }, 50);
    }
});
