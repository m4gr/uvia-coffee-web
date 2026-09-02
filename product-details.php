<?php
require_once 'config/database.php';
include 'includes/header.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
$tags = [];
$related_products = [];

if ($product_id > 0) {
    // Fetch product
    $stmt = $pdo->prepare("
        SELECT p.*, c.id as category_id, c.name_ar as category_name, c.name as category_name_en
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if ($product) {
        // Fetch tags
        $stmt = $pdo->prepare("SELECT * FROM product_tags WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $tags = $stmt->fetchAll();
        
        // Fetch related products (same category, different product)
        $stmt = $pdo->prepare("
            SELECT * FROM products 
            WHERE category_id = ? AND id != ? AND is_available = 1 
            ORDER BY RAND() LIMIT 4
        ");
        $stmt->execute([$product['category_id'], $product_id]);
        $related_products = $stmt->fetchAll();
    }
}
?>

<?php if ($product): ?>

<!-- Product Details Section -->
<div class="product-detail-page">
    <!-- Back Button -->
    <div class="detail-back">
        <a href="menu.php" class="back-btn">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للمنيو</span>
        </a>
    </div>

    <!-- Product Main Section -->
    <div class="product-detail-main">
        <!-- Product Image - Full Height -->
        <div class="product-detail-image-wrapper">
            <div class="product-detail-image">
                <?php if ($product['image']): ?>
                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name_ar']); ?>" id="mainProductImage">
                <?php else: ?>
                    <div class="image-placeholder">
                        <i class="fas fa-coffee"></i>
                    </div>
                <?php endif; ?>
                <?php if (!$product['is_available']): ?>
                    <span class="detail-unavailable-badge">
                        <i class="fas fa-times-circle"></i>
                        غير متوفر حالياً
                    </span>
                <?php endif; ?>
                <?php if ($product['is_featured']): ?>
                    <span class="detail-featured-badge">
                        <i class="fas fa-star"></i>
                        مميز
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="product-detail-info">
            <div class="detail-category-tag">
                <i class="fas fa-folder"></i>
                <?php echo htmlspecialchars($product['category_name'] ?? 'منتج'); ?>
            </div>
            
            <h1 class="detail-title">
                <?php echo htmlspecialchars($product['name_ar']); ?>
                <span class="detail-title-en"><?php echo htmlspecialchars($product['name']); ?></span>
            </h1>
            
            <div class="detail-price-section">
                <div class="detail-price">
                    <?php echo number_format($product['price'], 2); ?>
                    <span class="currency">SAR</span>
                </div>
                <?php if ($product['calories']): ?>
                    <div class="detail-calories">
                        <i class="fas fa-fire"></i>
                        <span><?php echo $product['calories']; ?> kcal</span>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($product['description_ar']): ?>
                <div class="detail-description">
                    <p><?php echo nl2br(htmlspecialchars($product['description_ar'])); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($tags)): ?>
                <div class="detail-tags">
                    <?php foreach ($tags as $tag): ?>
                        <span class="detail-tag"><?php echo htmlspecialchars($tag['tag_ar']); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Coffee Specs -->
            <?php if ($product['origin_ar'] || $product['process_method_ar'] || $product['roast_level_ar'] || $product['caffeine_level_ar'] || $product['flavor_notes_ar']): ?>
                <div class="detail-specs">
                    <h3 class="specs-title">
                        <i class="fas fa-info-circle"></i>
                        معلومات القهوة
                    </h3>
                    <div class="specs-grid">
                        <?php if ($product['origin_ar']): ?>
                            <div class="spec-item">
                                <span class="spec-label">
                                    <i class="fas fa-map-pin"></i>
                                    المنشأ
                                </span>
                                <span class="spec-value"><?php echo htmlspecialchars($product['origin_ar']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($product['process_method_ar']): ?>
                            <div class="spec-item">
                                <span class="spec-label">
                                    <i class="fas fa-flask"></i>
                                    المعالجة
                                </span>
                                <span class="spec-value"><?php echo htmlspecialchars($product['process_method_ar']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($product['roast_level_ar']): ?>
                            <div class="spec-item">
                                <span class="spec-label">
                                    <i class="fas fa-fire"></i>
                                    التحميص
                                </span>
                                <span class="spec-value"><?php echo htmlspecialchars($product['roast_level_ar']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($product['caffeine_level_ar']): ?>
                            <div class="spec-item">
                                <span class="spec-label">
                                    <i class="fas fa-bolt"></i>
                                    الكافيين
                                </span>
                                <span class="spec-value"><?php echo htmlspecialchars($product['caffeine_level_ar']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($product['flavor_notes_ar']): ?>
                            <div class="spec-item spec-full">
                                <span class="spec-label">
                                    <i class="fas fa-leaf"></i>
                                    الإيحاءات
                                </span>
                                <span class="spec-value"><?php echo htmlspecialchars($product['flavor_notes_ar']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($product['ingredients_ar']): ?>
                <div class="detail-ingredients">
                    <h3 class="ingredients-title">
                        <i class="fas fa-list-ul"></i>
                        المكونات
                    </h3>
                    <p><?php echo nl2br(htmlspecialchars($product['ingredients_ar'])); ?></p>
                </div>
            <?php endif; ?>
            
            <!-- Add to Cart Section -->
            <div class="detail-add-section">
                <div class="detail-quantity">
                    <span class="qty-label">الكمية</span>
                    <div class="qty-control">
                        <button class="qty-btn" onclick="changeDetailQuantity(-1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="qty-value" id="detailQty">1</span>
                        <button class="qty-btn" onclick="changeDetailQuantity(1)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                
                <button class="detail-add-btn" onclick="addFromDetail(<?php echo $product['id']; ?>)" <?php echo $product['is_available'] ? '' : 'disabled'; ?>>
                    <i class="fas fa-plus"></i>
                    <span><?php echo $product['is_available'] ? 'أضف للسلة' : 'غير متوفر'; ?></span>
                </button>
            </div>
            
            <?php if (!$product['is_available']): ?>
                <div class="detail-unavailable-notice">
                    <i class="fas fa-clock"></i>
                    هذا المنتج غير متوفر حالياً، لكن يمكنك متابعة المنتجات الأخرى
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
        <div class="related-products">
            <div class="related-header">
                <h2>
                    <i class="fas fa-random" style="color:var(--gold);"></i>
                    منتجات مشابهة
                </h2>
                <a href="menu.php" class="related-view-all">
                    عرض الكل <i class="fas fa-arrow-left"></i>
                </a>
            </div>
            <div class="related-grid">
                <?php foreach ($related_products as $related): ?>
                    <a href="product-details.php?id=<?php echo $related['id']; ?>" class="related-card">
                        <div class="related-image">
                            <?php if ($related['image']): ?>
                                <img src="assets/images/<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['name_ar']); ?>">
                            <?php else: ?>
                                <i class="fas fa-coffee"></i>
                            <?php endif; ?>
                        </div>
                        <div class="related-info">
                            <h4><?php echo htmlspecialchars($related['name_ar']); ?></h4>
                            <span class="related-price"><?php echo number_format($related['price'], 2); ?> SAR</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
/* ========================================
   Product Details Page - Enhanced Styles
   ======================================== */

.product-detail-page {
    padding: 0 20px 40px;
    max-width: 900px;
    margin: 0 auto;
}

/* Back Button */
.detail-back {
    padding: 16px 0 8px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: var(--transition);
    padding: 8px 4px;
}

.back-btn:hover {
    color: var(--gold);
    transform: translateX(-4px);
}

.back-btn i {
    font-size: 16px;
}

/* Product Main */
.product-detail-main {
    background: var(--white);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-light);
}

/* Product Image - 85% Height Fix */
.product-detail-image-wrapper {
    position: relative;
    overflow: hidden;
    height: 85vh;
    max-height: 85vh;
    min-height: 400px;
}

.product-detail-image {
    width: 100%;
    height: 100%;
    background: var(--beige);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.product-detail-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.product-detail-image .image-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.product-detail-image .image-placeholder i {
    font-size: 80px;
    color: var(--gold-light);
    opacity: 0.4;
}

.product-detail-image i {
    font-size: 64px;
    color: var(--gold-light);
    opacity: 0.4;
}

/* Scroll indicator */
.product-detail-image-wrapper::after {
    content: '↓ اسحب للأسفل للمزيد';
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(26, 16, 8, 0.7);
    color: var(--white);
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 500;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    animation: bounceDown 2s ease-in-out infinite;
    pointer-events: none;
    white-space: nowrap;
    opacity: 0.7;
}

@keyframes bounceDown {
    0%, 100% {
        transform: translateX(-50%) translateY(0);
    }
    50% {
        transform: translateX(-50%) translateY(8px);
    }
}

/* Badges */
.detail-unavailable-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(26, 16, 8, 0.85);
    color: var(--white);
    padding: 8px 18px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 10;
}

.detail-unavailable-badge i {
    color: #e74c3c;
    font-size: 18px;
}

.detail-featured-badge {
    position: absolute;
    top: 20px;
    left: 20px;
    background: var(--gold);
    color: var(--white);
    padding: 6px 16px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 16px rgba(201, 169, 110, 0.4);
    z-index: 10;
}

.detail-featured-badge i {
    font-size: 13px;
    color: var(--white);
}

/* Product Info */
.product-detail-info {
    padding: 24px;
}

.detail-category-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-muted);
    background: var(--beige);
    padding: 4px 14px;
    border-radius: 50px;
    margin-bottom: 8px;
}

.detail-title {
    font-size: clamp(24px, 4vw, 32px);
    font-weight: 700;
    color: var(--primary-dark);
    font-family: var(--font-display);
    margin-bottom: 2px;
}

.detail-title-en {
    font-size: 14px;
    color: var(--text-muted);
    font-weight: 400;
    font-family: var(--font-latin);
    direction: ltr;
    display: block;
}

.detail-price-section {
    display: flex;
    align-items: center;
    gap: 16px;
    margin: 8px 0 16px;
}

.detail-price {
    font-size: 28px;
    font-weight: 700;
    color: var(--gold);
}

.detail-price .currency {
    font-size: 16px;
    font-weight: 400;
    color: var(--text-muted);
}

.detail-calories {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: var(--text-muted);
    background: var(--beige);
    padding: 4px 14px;
    border-radius: 50px;
}

.detail-calories i {
    color: #e74c3c;
}

.detail-description {
    font-size: 15px;
    color: var(--text-muted);
    line-height: 1.8;
    margin: 12px 0 8px;
}

.detail-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin: 8px 0 16px;
}

.detail-tag {
    padding: 4px 14px;
    background: var(--beige);
    border-radius: 50px;
    font-size: 12px;
    font-weight: 500;
    color: var(--text-muted);
}

/* Specs */
.detail-specs {
    background: var(--cream);
    border-radius: var(--radius-sm);
    padding: 16px 18px;
    margin: 16px 0;
}

.specs-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--primary-dark);
    margin-bottom: 12px;
    font-family: var(--font-display);
}

.specs-title i {
    color: var(--gold);
    margin-left: 8px;
}

.specs-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.spec-item {
    display: flex;
    flex-direction: column;
    padding: 6px 8px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 8px;
}

.spec-item.spec-full {
    grid-column: 1 / -1;
}

.spec-label {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}

.spec-label i {
    font-size: 11px;
}

.spec-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--primary-dark);
    margin-top: 2px;
}

/* Ingredients */
.detail-ingredients {
    margin: 16px 0 20px;
}

.ingredients-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--primary-dark);
    margin-bottom: 6px;
    font-family: var(--font-display);
}

.ingredients-title i {
    color: var(--gold);
    margin-left: 8px;
}

.detail-ingredients p {
    font-size: 14px;
    color: var(--text-muted);
    line-height: 1.7;
}

/* Add Section */
.detail-add-section {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid var(--beige);
}

.detail-quantity {
    display: flex;
    align-items: center;
    gap: 16px;
}

.qty-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--primary-dark);
}

.qty-control {
    display: flex;
    align-items: center;
    gap: 8px;
}

.qty-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid var(--gold-light);
    background: transparent;
    font-size: 16px;
    color: var(--primary-brown);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.qty-btn:hover {
    background: var(--gold);
    color: var(--white);
    border-color: var(--gold);
}

.qty-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--primary-dark);
    min-width: 30px;
    text-align: center;
}

.detail-add-btn {
    width: 100%;
    padding: 16px;
    background: var(--gold);
    color: var(--white);
    border: none;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 700;
    font-family: var(--font-arabic);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.detail-add-btn:hover {
    background: #b8925a;
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(201, 169, 110, 0.3);
}

.detail-add-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.detail-unavailable-notice {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: #fde8e8;
    border-radius: var(--radius-sm);
    color: #e74c3c;
    font-size: 14px;
    margin-top: 12px;
}

.detail-unavailable-notice i {
    font-size: 18px;
}

/* Related Products */
.related-products {
    margin-top: 32px;
}

.related-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
}

.related-header h2 {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary-dark);
    font-family: var(--font-display);
}

.related-header h2 i {
    margin-left: 8px;
}

.related-view-all {
    font-size: 13px;
    color: var(--gold);
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: var(--transition);
}

.related-view-all:hover {
    color: #b8925a;
    transform: translateX(-2px);
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.related-card {
    background: var(--white);
    border-radius: var(--radius-sm);
    overflow: hidden;
    text-decoration: none;
    transition: var(--transition);
    box-shadow: var(--shadow-light);
    border: 1px solid rgba(201, 169, 110, 0.06);
}

.related-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow);
}

.related-image {
    width: 100%;
    aspect-ratio: 1/1;
    background: var(--beige);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.related-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-image i {
    font-size: 32px;
    color: var(--gold-light);
    opacity: 0.4;
}

.related-info {
    padding: 10px 12px;
}

.related-info h4 {
    font-size: 13px;
    font-weight: 600;
    color: var(--primary-dark);
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.related-price {
    font-size: 13px;
    font-weight: 700;
    color: var(--gold);
}

/* Toast Animation for Add */
@keyframes addedPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.detail-add-btn.added {
    animation: addedPulse 0.4s ease;
    background: #27ae60;
}

/* ========================================
   Responsive
   ======================================== */

@media (min-width: 768px) {
    .product-detail-page {
        padding: 0 40px 48px;
        max-width: 1000px;
    }
    
    .product-detail-main {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        border-radius: var(--radius);
    }
    
    .product-detail-image-wrapper {
        grid-column: 1 / 2;
        height: 85vh;
        max-height: 85vh;
        min-height: 500px;
    }
    
    .product-detail-image-wrapper::after {
        display: none;
    }
    
    .product-detail-info {
        grid-column: 2 / 3;
        padding: 32px 28px 28px;
        overflow-y: auto;
        max-height: 85vh;
    }
    
    .detail-add-section {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    
    .detail-quantity {
        flex: 0 0 auto;
    }
    
    .detail-add-btn {
        flex: 1;
        min-width: 200px;
    }
    
    .related-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
}

@media (min-width: 1024px) {
    .product-detail-page {
        max-width: 1200px;
        padding: 0 60px 56px;
    }
    
    .product-detail-image-wrapper {
        min-height: 600px;
    }
    
    .product-detail-info {
        padding: 40px 36px 32px;
    }
    
    .specs-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 767px) {
    .product-detail-image-wrapper {
        height: 85vh;
        max-height: 85vh;
        min-height: 350px;
    }
    
    .product-detail-image-wrapper::after {
        bottom: 20px;
        font-size: 11px;
        padding: 6px 16px;
    }
    
    .product-detail-info {
        padding: 18px 16px 20px;
    }
    
    .detail-price {
        font-size: 24px;
    }
    
    .specs-grid {
        grid-template-columns: 1fr;
    }
    
    .related-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .related-info h4 {
        font-size: 12px;
    }
    
    .related-price {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .product-detail-page {
        padding: 0 12px 32px;
    }
    
    .product-detail-image-wrapper {
        height: 85vh;
        max-height: 85vh;
        min-height: 300px;
    }
    
    .detail-unavailable-badge {
        top: 12px;
        right: 12px;
        font-size: 12px;
        padding: 6px 14px;
    }
    
    .detail-featured-badge {
        top: 12px;
        left: 12px;
        font-size: 11px;
        padding: 4px 12px;
    }
    
    .product-detail-info {
        padding: 14px 12px 16px;
    }
}

/* Custom scroll for product info on desktop */
@media (min-width: 768px) {
    .product-detail-info::-webkit-scrollbar {
        width: 3px;
    }
    
    .product-detail-info::-webkit-scrollbar-track {
        background: var(--beige);
    }
    
    .product-detail-info::-webkit-scrollbar-thumb {
        background: var(--gold);
        border-radius: 10px;
    }
}
</style>

<script>
// ========================================
// Product Details JavaScript
// ========================================

let detailQty = 1;

function changeDetailQuantity(delta) {
    detailQty = Math.max(1, detailQty + delta);
    const qtyEl = document.getElementById('detailQty');
    if (qtyEl) {
        qtyEl.textContent = detailQty;
    }
}

function addFromDetail(productId) {
    const productName = document.querySelector('.detail-title')?.textContent?.trim() || 'منتج';
    const priceText = document.querySelector('.detail-price')?.textContent?.trim() || '0';
    const price = parseFloat(priceText) || 0;
    
    // Add to cart
    if (typeof addToCart === 'function') {
        addToCart(productId, productName, price, detailQty);
    }
    
    // Animate button
    const btn = document.querySelector('.detail-add-btn');
    if (btn) {
        btn.classList.add('added');
        setTimeout(() => {
            btn.classList.remove('added');
        }, 600);
    }
    
    // Reset quantity
    detailQty = 1;
    const qtyEl = document.getElementById('detailQty');
    if (qtyEl) {
        qtyEl.textContent = '1';
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowUp') {
        e.preventDefault();
        changeDetailQuantity(1);
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        changeDetailQuantity(-1);
    }
});

// Image zoom on click (optional)
document.addEventListener('DOMContentLoaded', function() {
    const image = document.getElementById('mainProductImage');
    if (image) {
        image.style.cursor = 'zoom-in';
        image.addEventListener('click', function() {
            this.classList.toggle('zoomed');
            if (this.classList.contains('zoomed')) {
                this.style.objectFit = 'contain';
                this.style.cursor = 'zoom-out';
            } else {
                this.style.objectFit = 'cover';
                this.style.cursor = 'zoom-in';
            }
        });
    }
});
</script>

<?php else: ?>
    <!-- Product Not Found -->
    <div class="product-not-found">
        <div class="not-found-content">
            <div class="not-found-icon">
                <i class="fas fa-coffee"></i>
                <i class="fas fa-times-circle"></i>
            </div>
            <h2>المنتج غير موجود</h2>
            <p>عذراً، المنتج الذي تبحث عنه غير متوفر أو تم حذفه</p>
            <a href="menu.php" class="btn-primary">
                <i class="fas fa-utensils"></i>
                العودة للمنيو
            </a>
        </div>
    </div>

    <style>
    .product-not-found {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        padding: 40px 20px;
    }
    
    .not-found-content {
        text-align: center;
        max-width: 400px;
    }
    
    .not-found-icon {
        position: relative;
        display: inline-block;
        font-size: 80px;
        color: var(--gold-light);
        margin-bottom: 16px;
    }
    
    .not-found-icon .fa-times-circle {
        position: absolute;
        bottom: -4px;
        right: -20px;
        font-size: 32px;
        color: #e74c3c;
    }
    
    .not-found-content h2 {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 4px;
        font-family: var(--font-display);
    }
    
    .not-found-content p {
        color: var(--text-muted);
        font-size: 15px;
        margin-bottom: 20px;
    }
    </style>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>