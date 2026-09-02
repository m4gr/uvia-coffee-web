// ========================================
// UVIA Coffee - Cart Management
// ========================================

const CART_STORAGE_KEY = 'uvia_cart';

// ========================================
// Cart CRUD Operations
// ========================================

function getCart() {
    try {
        const data = localStorage.getItem(CART_STORAGE_KEY);
        return data ? JSON.parse(data) : [];
    } catch {
        return [];
    }
}

function saveCart(cart) {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
    updateCartBadge();
}

function addToCart(productId, name, price, quantity = 1) {
    const cart = getCart();
    const existing = cart.find(item => item.id === productId);
    
    if (existing) {
        existing.quantity += quantity;
    } else {
        cart.push({
            id: productId,
            name: name,
            price: price,
            quantity: quantity
        });
    }
    
    saveCart(cart);
    showToast('تمت إضافة المنتج للسلة ✓');
    updateCartUI();
}

function removeFromCart(productId) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== productId);
    saveCart(cart);
    updateCartUI();
}

function updateQuantity(productId, delta) {
    const cart = getCart();
    const item = cart.find(i => i.id === productId);
    
    if (item) {
        item.quantity = Math.max(0, item.quantity + delta);
        if (item.quantity === 0) {
            removeFromCart(productId);
            return;
        }
        saveCart(cart);
        updateCartUI();
    }
}

function clearCart() {
    // Show custom confirmation modal instead of browser confirm
    showConfirmationModal(
        'مسح السلة',
        'هل أنت متأكد من مسح جميع المنتجات من سلة المشتريات؟',
        function() {
            // On confirm - clear the cart
            saveCart([]);
            updateCartUI();
            showToast('تم مسح السلة بنجاح', 'fa-trash-alt');
        }
    );
}

function showConfirmationModal(title, message, onConfirm) {
    // Remove existing modal if any
    const existingModal = document.getElementById('customConfirmModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Create modal container
    const modal = document.createElement('div');
    modal.id = 'customConfirmModal';
    modal.className = 'confirm-modal-overlay';
    modal.innerHTML = `
        <div class="confirm-modal">
            <div class="confirm-modal-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 class="confirm-modal-title">${title}</h3>
            <p class="confirm-modal-message">${message}</p>
            <div class="confirm-modal-actions">
                <button class="confirm-btn-cancel" onclick="closeConfirmModal()">
                    <i class="fas fa-times"></i> إلغاء
                </button>
                <button class="confirm-btn-confirm" id="confirmDeleteBtn">
                    <i class="fas fa-check"></i> تأكيد
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add animation after a small delay
    requestAnimationFrame(() => {
        modal.classList.add('active');
    });
    
    // Handle confirm button click
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            closeConfirmModal();
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    }
    
    // Close on overlay click
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeConfirmModal();
        }
    });
    
    // Close on escape key
    document.addEventListener('keydown', function handleEsc(e) {
        if (e.key === 'Escape') {
            closeConfirmModal();
            document.removeEventListener('keydown', handleEsc);
        }
    });
}

function closeConfirmModal() {
    const modal = document.getElementById('customConfirmModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

function getCartTotal() {
    const cart = getCart();
    return cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
}

function getCartCount() {
    const cart = getCart();
    return cart.reduce((sum, item) => sum + item.quantity, 0);
}

// ========================================
// Cart UI Update
// ========================================

function updateCartUI() {
    const cart = getCart();
    const container = document.getElementById('cartItems');
    const emptyMsg = document.getElementById('cartEmpty');
    const summary = document.getElementById('cartSummary');
    const totalEl = document.getElementById('cartTotal');
    const countEl = document.getElementById('cartCount');
    
    if (!container) return;
    
    if (cart.length === 0) {
        if (emptyMsg) emptyMsg.style.display = '';
        if (summary) summary.style.display = 'none';
        container.innerHTML = '';
        return;
    }
    
    if (emptyMsg) emptyMsg.style.display = 'none';
    if (summary) summary.style.display = '';
    
    // Render items
    container.innerHTML = cart.map(item => `
        <div class="cart-item" data-id="${item.id}">
            <div class="item-image">
                <i class="fas fa-coffee"></i>
            </div>
            <div class="item-details">
                <div class="item-name">${item.name}</div>
                <div class="item-price">${item.price} SAR</div>
            </div>
            <div class="item-quantity">
                <button class="qty-btn" onclick="updateQuantity(${item.id}, -1)">
                    <i class="fas fa-minus"></i>
                </button>
                <span class="qty-number">${item.quantity}</span>
                <button class="qty-btn" onclick="updateQuantity(${item.id}, 1)">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <button class="item-remove" onclick="removeFromCart(${item.id})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `).join('');
    
    // Update totals
    const total = getCartTotal();
    const count = getCartCount();
    
    if (totalEl) totalEl.textContent = total.toFixed(2);
    if (countEl) countEl.textContent = count;
    
    updateCartBadge();
}

// ========================================
// Cart Page Initialization
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the cart page
    if (document.getElementById('cartItems')) {
        updateCartUI();
    }
});