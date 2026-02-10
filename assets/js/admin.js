// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const tabName = btn.dataset.tab;
        
        // Update active tab button
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        // Update active tab content
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.getElementById(tabName + '-tab').classList.add('active');
        
        // Load data for the tab
        if (tabName === 'products') loadProducts();
        else if (tabName === 'orders') loadOrders();
        else if (tabName === 'contacts') loadContacts();
        else if (tabName === 'users') loadUsers();
    });
});

// Load products
async function loadProducts() {
    try {
        const response = await fetch('api/admin.php?action=products');
        const data = await response.json();
        
        const container = document.getElementById('admin-products');
        container.innerHTML = '';
        
        if (data.error) {
            container.innerHTML = `<p style="color:#f44336;">Error: ${data.error}</p>`;
            return;
        }
        
        if (!data.products || data.products.length === 0) {
            container.innerHTML = '<p style="color:#999;">Žiadne produkty</p>';
            return;
        }
        
        data.products.forEach(product => {
            const div = document.createElement('div');
            div.className = 'admin-product-item';
            div.innerHTML = `
                ${product.Obrazok && product.Obrazok.length > 0 ? `<img src="api/image.php?id=${product.ID}" alt="${product.Meno}">` : '<div style="width:80px;height:60px;background:#333;border-radius:5px;margin-right:15px;"></div>'}
                <div class="admin-product-info">
                    <strong>${product.Meno}</strong><br>
                    <span style="color:#ccc;">${product.Cena}€</span>
                </div>
                <div class="admin-actions">
                    <button class="edit-btn" onclick="editProduct(${product.ID})">Upraviť</button>
                </div>
            `;
            container.appendChild(div);
        });
    } catch (error) {
        console.error('Error loading products:', error);
        const container = document.getElementById('admin-products');
        container.innerHTML = `<p style="color:#f44336;">Error loading products: ${error.message}</p>`;
    }
}

// Load orders
async function loadOrders() {
    try {
        const response = await fetch('api/admin.php?action=orders');
        const data = await response.json();
        
        const container = document.getElementById('admin-orders');
        container.innerHTML = '';
        
        if (!data.orders || data.orders.length === 0) {
            container.innerHTML = '<p style="color:#999;">Žiadne objednávky</p>';
            return;
        }
        
        data.orders.forEach(order => {
            const div = document.createElement('div');
            div.className = 'admin-order-item';
            div.innerHTML = `
                <div style="flex:1;cursor:pointer;" onclick="toggleOrderItems(${order.ID})">
                    <strong>#${order.ID} - ${order.customer_name}</strong> (${order.email})<br>
                    <span style="color:#ccc;">Suma: ${order.total_price}€</span>
                    <span class="status-badge status-${order.status || 'new'}" style="margin-left:10px;">${order.status || 'new'}</span><br>
                    <span style="color:#999;font-size:12px;">${order.created_at}</span>
                    <div class="order-items">Adresa: ${order.address}</div>
                    ${order.note ? `<div class="order-items">Poznámka: ${order.note}</div>` : ''}
                    <div id="order-items-${order.ID}" class="order-items-detail" style="display:none;margin-top:10px;padding:10px;background:rgba(0,0,0,0.2);border-radius:5px;"></div>
                </div>
                <div class="admin-actions">
                    <select onchange="updateOrderStatus(${order.ID}, this.value)" style="padding:8px;border-radius:5px;background:#2a2a2a;color:#eee;border:1px solid #444;margin-right:10px;">
                        <option value="new" ${order.status === 'new' ? 'selected' : ''}>Nová</option>
                        <option value="processing" ${order.status === 'processing' ? 'selected' : ''}>Spracováva sa</option>
                        <option value="shipped" ${order.status === 'shipped' ? 'selected' : ''}>Odoslaná</option>
                        <option value="delivered" ${order.status === 'delivered' ? 'selected' : ''}>Doručená</option>
                        <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Zrušená</option>
                    </select>
                    <button class="delete-btn" onclick="deleteOrder(${order.ID})">Zmazať</button>
                </div>
            `;
            container.appendChild(div);
        });
    } catch (error) {
        console.error('Error loading orders:', error);
    }
}

// Toggle order items display
async function toggleOrderItems(orderId) {
    const itemsDiv = document.getElementById(`order-items-${orderId}`);
    
    if (itemsDiv.style.display === 'none') {
        try {
            const response = await fetch(`api/admin.php?action=order_items&id=${orderId}`);
            const data = await response.json();
            
            if (data.items && data.items.length > 0) {
                itemsDiv.innerHTML = '<strong>Položky:</strong><br>' + data.items.map(item => 
                    `<div style="margin:5px 0;">${item.quantity}x ${item.product_name} - ${item.price}€</div>`
                ).join('');
                itemsDiv.style.display = 'block';
            } else {
                itemsDiv.innerHTML = '<span style="color:#999;">Žiadne položky</span>';
                itemsDiv.style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading order items:', error);
        }
    } else {
        itemsDiv.style.display = 'none';
    }
}

// Update order status
async function updateOrderStatus(orderId, status) {
    try {
        const response = await fetch('api/admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'update_order_status',
                id: orderId,
                status: status
            })
        });
        
        const data = await response.json();
        if (data.success) {
            loadOrders();
        } else {
            alert(data.error || 'Chyba pri aktualizácii stavu');
        }
    } catch (error) {
        console.error('Error updating order status:', error);
        alert('Chyba pri aktualizácii stavu');
    }
}

// Delete order
async function deleteOrder(orderId) {
    if (!confirm('Naozaj chcete zmazať túto objednávku?')) return;
    
    try {
        const response = await fetch('api/admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'delete_order',
                id: orderId
            })
        });
        
        const data = await response.json();
        if (data.success) {
            loadOrders();
        } else {
            alert(data.error || 'Chyba pri mazaní objednávky');
        }
    } catch (error) {
        console.error('Error deleting order:', error);
        alert('Chyba pri mazaní objednávky');
    }
}

// Load contacts
async function loadContacts() {
    try {
        const response = await fetch('api/admin.php?action=contacts');
        const data = await response.json();
        
        const container = document.getElementById('admin-contacts');
        container.innerHTML = '';
        
        if (data.contacts.length === 0) {
            container.innerHTML = '<p style="color:#999;">Žiadne kontaktné správy</p>';
            return;
        }
        
        data.contacts.forEach(contact => {
            const div = document.createElement('div');
            div.className = 'admin-contact-item';
            div.innerHTML = `
                <div style="flex:1;">
                    <strong>${contact.name}</strong> (${contact.email})<br>
                    <span class="status-badge status-${contact.status}">${contact.status}</span><br>
                    <p style="margin:10px 0;color:#ccc;">${contact.message}</p>
                    <span style="color:#999;font-size:12px;">${contact.created_at}</span>
                </div>
                <div class="admin-actions">
                    <button class="edit-btn" onclick="markAsRead(${contact.ID})">Označiť ako prečítané</button>
                </div>
            `;
            container.appendChild(div);
        });
    } catch (error) {
        console.error('Error loading contacts:', error);
    }
}

// Load users
async function loadUsers() {
    try {
        const response = await fetch('api/admin.php?action=users');
        const data = await response.json();
        
        const container = document.getElementById('admin-users');
        container.innerHTML = '';
        
        if (!data.users || data.users.length === 0) {
            container.innerHTML = '<p style="color:#999;">Žiadni používatelia</p>';
            return;
        }
        
        data.users.forEach(user => {
            const div = document.createElement('div');
            div.className = 'admin-user-item';
            div.innerHTML = `
                <div style="flex:1;">
                    <strong>${user.name}</strong> (${user.email})<br>
                    ${user.is_admin ? '<span class="status-badge status-new">Admin</span>' : '<span class="status-badge status-read">User</span>'}
                    <span style="color:#999;font-size:12px;display:block;margin-top:5px;">Registrovaný: ${user.created_at}</span>
                </div>
                <div class="admin-actions">
                    <button class="edit-btn" onclick="toggleAdmin(${user.ID}, ${user.is_admin})">
                        ${user.is_admin ? 'Odobrať admin' : 'Pridať admin'}
                    </button>
                    <button class="edit-btn" onclick="editUser(${user.ID})">Upraviť</button>
                    <button class="delete-btn" onclick="deleteUser(${user.ID})">Zmazať</button>
                </div>
            `;
            container.appendChild(div);
        });
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

// Toggle admin status
async function toggleAdmin(userId, currentStatus) {
    try {
        const response = await fetch('api/admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'toggle_admin',
                id: userId,
                is_admin: !currentStatus
            })
        });
        
        const data = await response.json();
        if (data.success) {
            loadUsers();
        } else {
            alert(data.error || 'Chyba pri zmene admin statusu');
        }
    } catch (error) {
        console.error('Error toggling admin:', error);
        alert('Chyba pri zmene admin statusu');
    }
}

// Edit user
async function editUser(userId) {
    try {
        const response = await fetch(`api/admin.php?action=user&id=${userId}`);
        const data = await response.json();
        const user = data.user;
        
        const newName = prompt('Zadajte nové meno:', user.name);
        if (newName === null) return;
        
        const newEmail = prompt('Zadajte nový email:', user.email);
        if (newEmail === null) return;
        
        const updateResponse = await fetch('api/admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'update_user',
                id: userId,
                name: newName,
                email: newEmail
            })
        });
        
        const updateData = await updateResponse.json();
        if (updateData.success) {
            alert('Používateľ bol úspešne aktualizovaný!');
            loadUsers();
        } else {
            alert(updateData.error || 'Chyba pri aktualizácii používateľa');
        }
    } catch (error) {
        console.error('Error editing user:', error);
        alert('Chyba pri úprave používateľa');
    }
}

// Delete user
async function deleteUser(userId) {
    if (!confirm('Naozaj chcete zmazať tohto používateľa?')) return;
    
    try {
        const response = await fetch('api/admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'delete_user',
                id: userId
            })
        });
        
        const data = await response.json();
        if (data.success) {
            loadUsers();
        } else {
            alert(data.error || 'Chyba pri mazaní používateľa');
        }
    } catch (error) {
        console.error('Error deleting user:', error);
        alert('Chyba pri mazaní používateľa');
    }
}

// Edit product
async function editProduct(id) {
    try {
        const response = await fetch(`api/admin.php?action=product&id=${id}`);
        const data = await response.json();
        const product = data.product;
        
        document.getElementById('edit-product-id').value = product.ID;
        document.getElementById('edit-product-name').value = product.Meno;
        document.getElementById('edit-product-description').value = product.Popis;
        document.getElementById('edit-product-price').value = product.Cena;
        
        const preview = document.getElementById('current-image-preview');
        if (product.Obrazok) {
            preview.innerHTML = `<img src="api/image.php?id=${product.ID}" alt="Current image">`;
        } else {
            preview.innerHTML = '<p style="color:#999;">Žiadny obrázok</p>';
        }
        
        document.getElementById('edit-product-modal').style.display = 'flex';
    } catch (error) {
        console.error('Error loading product:', error);
    }
}

// Save product
document.getElementById('edit-product-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('edit-product-id').value;
    const name = document.getElementById('edit-product-name').value;
    const description = document.getElementById('edit-product-description').value;
    const price = document.getElementById('edit-product-price').value;
    const imageFile = document.getElementById('edit-product-image').files[0];
    
    try {
        // Update product details
        const response = await fetch('api/admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'update_product',
                id, name, description, price
            })
        });
        
        const data = await response.json();
        
        // Upload image if provided
        if (imageFile) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('image', imageFile);
            
            await fetch('api/image.php', {
                method: 'POST',
                body: formData
            });
        }
        
        if (data.success) {
            alert('Produkt bol úspešne aktualizovaný!');
            document.getElementById('edit-product-modal').style.display = 'none';
            loadProducts();
        } else {
            alert(data.error || 'Chyba pri aktualizácii produktu');
        }
    } catch (error) {
        console.error('Error updating product:', error);
        alert('Chyba pri aktualizácii produktu');
    }
});

// Mark contact as read
async function markAsRead(id) {
    try {
        const response = await fetch('api/admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'mark_contact_read',
                id
            })
        });
        
        const data = await response.json();
        if (data.success) {
            loadContacts();
        }
    } catch (error) {
        console.error('Error marking as read:', error);
    }
}

// Close modal
document.querySelector('#edit-product-modal .close').addEventListener('click', () => {
    document.getElementById('edit-product-modal').style.display = 'none';
});

// Load initial data
loadProducts();
