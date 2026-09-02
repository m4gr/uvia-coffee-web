// ========================================
// UVIA Coffee - Main JavaScript
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart badge
    updateCartBadge();
    
    // Modal close handlers
    const modalOverlay = document.getElementById('productModal');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modalOverlay.classList.contains('active')) {
                closeModal();
            }
        });
    }
    
});

// ========================================
// Toast Notification
// ========================================

function showToast(message, icon = 'fa-check-circle') {
    // Remove existing toast
    const existingToast = document.getElementById('toast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create new toast
    const toast = document.createElement('div');
    toast.id = 'toast';
    toast.className = 'toast';
    toast.innerHTML = `
        <div class="toast-content">
            <i class="toast-icon fas ${icon}"></i>
            <span class="toast-message">${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Show with animation
    requestAnimationFrame(() => {
        toast.classList.add('show');
    });
    
    // Auto hide after 3 seconds
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 400);
    }, 3000);
}

// ========================================
// Cart Badge
// ========================================

function updateCartBadge() {
    const cart = getCart();
    const total = cart.reduce((sum, item) => sum + item.quantity, 0);
    const badge = document.getElementById('cartBadge');
    
    if (badge) {
        if (total > 0) {
            badge.textContent = total;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}

// ========================================
// Modal
// ========================================

function openModal(productId) {
    const modal = document.getElementById('productModal');
    if (!modal) return;
    
    // Load product data
    loadProductDetails(productId);
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('productModal');
    if (!modal) return;
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

// ========================================
// Product Details Loading
// ========================================

// ========================================
// Product Details Loading - Enhanced
// ========================================

function loadProductDetails(productId) {
    // Show loading state
    const modal = document.getElementById('productModal');
    if (modal) {
        modal.innerHTML = `
            <div class="modal-content" style="position:relative;padding:40px;text-align:center;">
                <div class="modal-handle"></div>
                <div style="padding:40px 0;">
                    <i class="fas fa-spinner fa-spin" style="font-size:40px;color:var(--gold);"></i>
                    <p style="margin-top:16px;color:var(--text-muted);">جاري تحميل المنتج...</p>
                </div>
            </div>
        `;
    }
    
    // Use absolute URL path
    const url = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '') + '/product.php?id=' + productId;
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                renderProductModal(data.product);
            } else {
                showToast(data.message || 'حدث خطأ في تحميل المنتج', 'fa-exclamation-circle');
                closeModal();
            }
        })
        .catch(error => {
            console.error('Error loading product:', error);
            showToast('حدث خطأ في الاتصال بالخادم', 'fa-exclamation-circle');
            closeModal();
        });
}

function renderProductModal(product) {
    const modal = document.getElementById('productModal');
    if (!modal) return;
    
    const imageHtml = product.image 
        ? `<img src="assets/images/${product.image}" alt="${product.name_ar}">`
        : `<i class="fas fa-coffee placeholder-icon"></i>`;
    
    const tagsHtml = product.tags && product.tags.length > 0
        ? product.tags.map(tag => `<span class="tag">${tag.tag_ar}</span>`).join('')
        : '';
    
    const detailsHtml = `
        ${product.origin_ar ? `
            <div class="detail-section">
                <h4><i class="fas fa-map-pin"></i> المنشأ</h4>
                <p>${product.origin_ar}</p>
            </div>
        ` : ''}
        ${product.process_method_ar ? `
            <div class="detail-section">
                <h4><i class="fas fa-flask"></i> المعالجة</h4>
                <p>${product.process_method_ar}</p>
            </div>
        ` : ''}
        ${product.roast_level_ar ? `
            <div class="detail-section">
                <h4><i class="fas fa-fire"></i> التحميص</h4>
                <p>${product.roast_level_ar}</p>
            </div>
        ` : ''}
        ${product.caffeine_level_ar ? `
            <div class="detail-section">
                <h4><i class="fas fa-bolt"></i> الكافيين</h4>
                <p>${product.caffeine_level_ar}</p>
            </div>
        ` : ''}
        ${product.flavor_notes_ar ? `
            <div class="detail-section">
                <h4><i class="fas fa-leaf"></i> الإيحاءات</h4>
                <p>${product.flavor_notes_ar}</p>
            </div>
        ` : ''}
        ${product.ingredients_ar ? `
            <div class="detail-section">
                <h4><i class="fas fa-list"></i> المكونات</h4>
                <p>${product.ingredients_ar}</p>
            </div>
        ` : ''}
    `;
    
    modal.innerHTML = `
        <div class="modal-content" style="position:relative;">
            <div class="modal-handle"></div>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-product-image">
                ${imageHtml}
            </div>
            
            <div class="modal-product-info">
                <div class="product-name">${product.name_ar}</div>
                <span class="product-name-en">${product.name}</span>
                
                <div class="price-calories">
                    <span class="price">${product.price} <span style="font-size:14px;font-weight:400;">SAR</span></span>
                    ${product.calories ? `<span class="calories"><i class="fas fa-utensils"></i> ${product.calories} kcal</span>` : ''}
                </div>
                
                ${product.description_ar ? `<p class="description">${product.description_ar}</p>` : ''}
                
                ${tagsHtml ? `<div class="tags">${tagsHtml}</div>` : ''}
                
                ${detailsHtml}
                
                <div class="modal-quantity">
                    <label>الكمية</label>
                    <div class="qty-control">
                        <button onclick="changeQuantity(-1)" ${!product.is_available ? 'disabled' : ''}>
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="qty-value" id="modalQty">1</span>
                        <button onclick="changeQuantity(1)" ${!product.is_available ? 'disabled' : ''}>
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                
                <button class="modal-add-cart" onclick="addFromModal(${product.id})" ${!product.is_available ? 'disabled' : ''}>
                    <i class="fas fa-plus"></i>
                    ${product.is_available ? 'أضف للسلة' : 'غير متوفر'}
                </button>
                
                ${!product.is_available ? '<p style="color:#e74c3c;font-size:13px;text-align:center;margin-top:8px;">هذا المنتج غير متوفر حالياً</p>' : ''}
            </div>
        </div>
    `;
    
    // Store product data for modal
    modal.dataset.productId = product.id;
    modal.dataset.price = product.price;
}

// ========================================
// Modal Quantity
// ========================================

let currentQty = 1;

function changeQuantity(delta) {
    currentQty = Math.max(1, currentQty + delta);
    const qtyEl = document.getElementById('modalQty');
    if (qtyEl) {
        qtyEl.textContent = currentQty;
    }
}

function addFromModal(productId) {
    const modal = document.getElementById('productModal');
    if (!modal) return;
    
    const price = parseFloat(modal.dataset.price);
    const qty = currentQty;
    
    // Get product name from modal
    const nameEl = modal.querySelector('.product-name');
    const name = nameEl ? nameEl.textContent : 'منتج';
    
    addToCart(productId, name, price, qty);
    closeModal();
    currentQty = 1;
}

// ========================================
// Category Filtering
// ========================================

function filterCategory(categoryId, element) {
    // Update active state
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    if (element) {
        element.classList.add('active');
    }
    
    // Filter products
    document.querySelectorAll('.product-card').forEach(card => {
        const catId = card.dataset.categoryId;
        if (categoryId === 'all' || catId == categoryId) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}