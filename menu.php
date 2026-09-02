<?php
require_once 'config/database.php';
include 'includes/header.php';

// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY display_order ASC");
$categories = $stmt->fetchAll();

// Fetch products with their categories and tags
$stmt = $pdo->query("
    SELECT p.*, c.id as category_id, c.name_ar as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.display_order ASC, p.id DESC
");
$products = $stmt->fetchAll();

// Fetch tags for products
$tagsByProduct = [];
if ($products) {
    $productIds = array_column($products, 'id');
    $placeholders = rtrim(str_repeat('?,', count($productIds)), ',');
    $stmt = $pdo->prepare("SELECT * FROM product_tags WHERE product_id IN ($placeholders)");
    $stmt->execute($productIds);
    while ($tag = $stmt->fetch()) {
        $tagsByProduct[$tag['product_id']][] = $tag;
    }
}

// Get featured products
$featuredProducts = array_filter($products, function($p) {
    return $p['is_featured'] == 1;
});
?>

<!-- Hero Section -->
<div class="menu-hero">
    <div class="menu-hero-content">
        <div class="menu-hero-badge">
            <i class="fas fa-mug-hot"></i>
            <span>تشكيلتنا</span>
        </div>
        <h1 class="menu-hero-title">
            اكتشف <span class="highlight">قهوتك</span> المثالية
        </h1>
        <p class="menu-hero-subtitle">
            نخبة من أجود أنواع القهوة والحلويات، تُحضّر بعناية لتناسب كل الأذواق
        </p>
    </div>
    <div class="menu-hero-decoration">
        <div class="hero-circle"></div>
        <div class="hero-circle-2"></div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="menu-toolbar">
    <div class="search-wrapper">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="ابحث عن منتج..." class="search-input">
        <button class="search-clear" id="searchClear" style="display:none;">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="filter-trigger" id="filterToggle">
        <i class="fas fa-sliders-h"></i>
        <span>فلتر</span>
    </div>
</div>

<!-- Categories Scroll -->
<div class="categories-scroll" id="categoriesScroll">
    <button class="category-btn active" data-category="all">
        <i class="fas fa-th"></i>
        <span>الكل</span>
    </button>
    <?php foreach ($categories as $cat): ?>
        <button class="category-btn" data-category="<?php echo $cat['id']; ?>">
            <i class="fas <?php echo $cat['icon'] ?? 'fa-tag'; ?>"></i>
            <span><?php echo htmlspecialchars($cat['name_ar']); ?></span>
        </button>
    <?php endforeach; ?>
</div>

<!-- Featured Products Section -->
<!-- Featured Products Section -->
<?php if (count($featuredProducts) > 0): ?>
    <div class="featured-section">
        <div class="section-header">
            <h2>
                <i class="fas fa-star" style="color:var(--gold);"></i>
                المنتجات المميزة
            </h2>
            <span class="section-badge">الأكثر طلباً</span>
        </div>
        <div class="featured-scroll" id="featuredScroll">
            <?php foreach ($featuredProducts as $product): 
                $productTags = $tagsByProduct[$product['id']] ?? [];
            ?>
                <a href="product-details.php?id=<?php echo $product['id']; ?>" class="featured-card">
                    <div class="featured-image">
                        <?php if ($product['image']): ?>
                            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name_ar']); ?>">
                        <?php else: ?>
                            <i class="fas fa-coffee"></i>
                        <?php endif; ?>
                        <?php if (!$product['is_available']): ?>
                            <span class="unavailable-badge">غير متوفر</span>
                        <?php endif; ?>
                        <div class="featured-overlay">
                            <button class="featured-quick-add" onclick="event.stopPropagation(); event.preventDefault(); addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name_ar']); ?>', <?php echo $product['price']; ?>, 1)" <?php echo $product['is_available'] ? '' : 'disabled'; ?>>
                                <i class="fas fa-plus" style=""></i>
                            </button>
                        </div>
                    </div>
                    <div class="featured-info">
                        <h4><?php echo htmlspecialchars($product['name_ar']); ?></h4>
                        <span class="featured-price"><?php echo number_format($product['price'], 2); ?> SAR</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Products Grid -->
<div class="products-section">
    <div class="section-header">
        <h2>
            <i class="fas fa-utensils" style="color:var(--gold);"></i>
            جميع المنتجات
        </h2>
        <span class="product-count" id="productCount"><?php echo count($products); ?> منتج</span>
    </div>
    
    <div class="products-grid" id="productsGrid">
        <?php foreach ($products as $product): 
            $productTags = $tagsByProduct[$product['id']] ?? [];
        ?>
            <div class="product-card" data-category-id="<?php echo $product['category_id']; ?>" data-product-name="<?php echo htmlspecialchars($product['name_ar']); ?>">
                <a href="product-details.php?id=<?php echo $product['id']; ?>" class="product-card-link">
                    <div class="product-card-inner">
                        <div class="product-image-wrapper">
                            <div class="product-image">
                                <?php if ($product['image']): ?>
                                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name_ar']); ?>" loading="lazy">
                                <?php else: ?>
                                    <i class="fas fa-coffee placeholder-icon"></i>
                                <?php endif; ?>
                                <?php if (!$product['is_available']): ?>
                                    <span class="unavailable-badge">
                                        <i class="fas fa-times-circle"></i>
                                        غير متوفر
                                    </span>
                                <?php endif; ?>
                                <?php if ($product['is_featured']): ?>
                                    <span class="featured-badge">
                                        <i class="fas fa-star"></i>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="product-info">
                            <div class="product-name">
                                <?php echo htmlspecialchars($product['name_ar']); ?>
                                <span class="product-name-en"><?php echo htmlspecialchars($product['name']); ?></span>
                            </div>
                            
                            <div class="product-meta">
                                <span class="product-price">
                                    <?php echo number_format($product['price'], 2); ?>
                                    <small>SAR</small>
                                </span>
                                <?php if ($product['calories']): ?>
                                    <span class="product-calories">
                                        <i class="fas fa-fire"></i>
                                        <?php echo $product['calories']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($product['description_ar']): ?>
                                <p class="product-description">
                                    <?php echo htmlspecialchars(mb_substr($product['description_ar'], 0, 50)) . (mb_strlen($product['description_ar']) > 50 ? '...' : ''); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($productTags)): ?>
                                <div class="product-tags">
                                    <?php foreach (array_slice($productTags, 0, 3) as $tag): ?>
                                        <span class="tag"><?php echo htmlspecialchars($tag['tag_ar']); ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($productTags) > 3): ?>
                                        <span class="tag tag-more">+<?php echo count($productTags) - 3; ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-footer">
                                <span class="product-category">
                                    <i class="fas fa-folder"></i>
                                    <?php echo htmlspecialchars($product['category_name'] ?? ''); ?>
                                </span>
                                <button class="btn-add-cart" onclick="event.stopPropagation(); event.preventDefault(); addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name_ar']); ?>', <?php echo $product['price']; ?>, 1)" <?php echo $product['is_available'] ? '' : 'disabled'; ?>>
                                    <i class="fas fa-plus"></i>
                                    <span><?php echo $product['is_available'] ? 'أضف' : ''; ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Empty State -->
    <div class="empty-state" id="emptyState" style="display:none;">
        <i class="fas fa-search"></i>
        <h3>لا توجد منتجات</h3>
        <p>لم نجد أي منتج يطابق بحثك</p>
    </div>
</div>

<!-- Product Modal -->
<div class="modal-overlay" id="productModal">
    <!-- Content loaded dynamically -->
</div>

<!-- Filter Modal -->
<div class="filter-modal" id="filterModal">
    <div class="filter-modal-content">
        <div class="filter-header">
            <h3><i class="fas fa-sliders-h" style="color:var(--gold);"></i> تصفية المنتجات</h3>
            <button class="filter-close" onclick="closeFilterModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="filter-body">
            <div class="filter-group">
                <label>التصنيف</label>
                <div class="filter-options" id="filterOptions">
                    <button class="filter-option active" data-filter="all">الكل</button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="filter-option" data-filter="<?php echo $cat['id']; ?>">
                            <?php echo htmlspecialchars($cat['name_ar']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="filter-group">
                <label>الحالة</label>
                <div class="filter-options">
                    <button class="filter-option active" data-status="all">الكل</button>
                    <button class="filter-option" data-status="available">متوفر</button>
                    <button class="filter-option" data-status="unavailable">غير متوفر</button>
                </div>
            </div>
            <div class="filter-group">
                <label>السعر</label>
                <div class="filter-price-range">
                    <input type="range" id="priceRange" min="0" max="100" value="100">
                    <div class="price-labels">
                        <span>0 SAR</span>
                        <span id="priceRangeLabel">100+ SAR</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-footer">
            <button class="filter-reset" onclick="resetFilters()">
                <i class="fas fa-undo"></i> إعادة تعيين
            </button>
            <button class="filter-apply" onclick="applyFilters()">
                <i class="fas fa-check"></i> تطبيق
            </button>
        </div>
    </div>
</div>

<style>
/* ========================================
   Menu Page Enhanced Styles
   ======================================== */
a{
    text-decoration:none;
}
/* Menu Hero */
.menu-hero {
    background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/images/bannar_menu.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    padding: 40px 20px 32px;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 0;
}

.menu-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -30%;
    width: 80%;
    height: 200%;
    background: radial-gradient(circle, rgba(201, 169, 110, 0.06) 0%, transparent 70%);
    pointer-events: none;
}

.menu-hero::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 40%;
    background: linear-gradient(to top, var(--beige) 0%, transparent 100%);
    pointer-events: none;
}

.menu-hero-content {
    position: relative;
    z-index: 1;
}

.menu-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    background: rgba(201, 169, 110, 0.15);
    border: 1px solid rgba(201, 169, 110, 0.2);
    border-radius: 50px;
    color: var(--gold);
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 12px;
}

.menu-hero-badge i {
    font-size: 14px;
}

.menu-hero-title {
    font-size: clamp(28px, 6vw, 42px);
    font-weight: 700;
    color: var(--white);
    line-height: 1.2;
    margin-bottom: 8px;
    font-family: var(--font-display);
}

.menu-hero-title .highlight {
    color: var(--gold);
}

.menu-hero-subtitle {
    font-size: clamp(14px, 2vw, 18px);
    color: rgba(255, 255, 255, 0.6);
    font-weight: 300;
    line-height: 1.8;
    max-width: 500px;
    margin: 0 auto;
}

.menu-hero-decoration {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    overflow: hidden;
}

.hero-circle {
    position: absolute;
    top: -100px;
    right: -100px;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(201, 169, 110, 0.05) 0%, transparent 70%);
}

.hero-circle-2 {
    position: absolute;
    bottom: -150px;
    left: -150px;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(201, 169, 110, 0.03) 0%, transparent 70%);
}

/* Toolbar */
.menu-toolbar {
    display: flex;
    gap: 12px;
    padding: 16px 20px;
    background: var(--white);
    border-bottom: 1px solid rgba(201, 169, 110, 0.08);
    position: sticky;
    top: 0;
    z-index: 90;
}

.search-wrapper {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    right: 14px;
    color: var(--text-muted);
    font-size: 14px;
}

.search-input {
    width: 100%;
    padding: 10px 42px 10px 14px;
    border: 2px solid var(--beige);
    border-radius: 12px;
    font-size: 14px;
    font-family: var(--font-arabic);
    background: var(--beige);
    transition: var(--transition);
}

.search-input:focus {
    outline: none;
    border-color: var(--gold);
    background: var(--white);
    box-shadow: 0 0 0 4px rgba(201, 169, 110, 0.1);
}

.search-clear {
    position: absolute;
    left: 14px;
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    font-size: 16px;
    padding: 4px;
}

.filter-trigger {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    background: var(--beige);
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-muted);
    cursor: pointer;
    transition: var(--transition);
    font-family: var(--font-arabic);
    border: 2px solid transparent;
    white-space: nowrap;
}

.filter-trigger:hover {
    background: var(--gold-light);
    border-color: var(--gold);
}

/* Categories Scroll - Enhanced */
.categories-scroll {
    display: flex;
    gap: 8px;
    padding: 12px 20px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
    background: var(--white);
    border-bottom: 1px solid rgba(201, 169, 110, 0.06);
    position: sticky;
    top: 68px;
    z-index: 89;
}

.categories-scroll::-webkit-scrollbar {
    height: 2px;
}

.categories-scroll::-webkit-scrollbar-thumb {
    background: var(--gold);
    border-radius: 10px;
}

.category-btn {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    background: var(--beige);
    border: 2px solid transparent;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 500;
    font-family: var(--font-arabic);
    color: var(--text-muted);
    cursor: pointer;
    transition: var(--transition);
    white-space: nowrap;
}

.category-btn i {
    font-size: 13px;
}

.category-btn:hover {
    background: var(--gold-light);
    color: var(--primary-brown);
    transform: translateY(-1px);
}

.category-btn.active {
    background: var(--gold);
    color: var(--white);
    border-color: var(--gold);
    box-shadow: 0 4px 16px rgba(201, 169, 110, 0.3);
}

/* Featured Section */
.featured-section {
    padding: 20px 20px 12px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
}

.section-header h2 {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary-dark);
    font-family: var(--font-display);
}

.section-header h2 i {
    margin-left: 8px;
}

.section-badge {
    font-size: 11px;
    padding: 4px 12px;
    background: var(--gold-light);
    color: var(--gold);
    border-radius: 50px;
    font-weight: 600;
}

.product-count {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 400;
}

.featured-scroll {
    display: flex;
    gap: 14px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
    padding-bottom: 8px;
}

.featured-scroll::-webkit-scrollbar {
    height: 2px;
}

.featured-scroll::-webkit-scrollbar-thumb {
    background: var(--gold);
    border-radius: 10px;
}

.featured-card {
    flex-shrink: 0;
    width: 140px;
    background: var(--white);
    border-radius: var(--radius-sm);
    overflow: hidden;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: var(--shadow-light);
}

.featured-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow);
}

.featured-image {
    position: relative;
    width: 140px;
    height: 140px;
    background: var(--beige);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.featured-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.featured-image i {
    font-size: 40px;
    color: var(--gold-light);
    opacity: 0.5;
}

.featured-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 0px;
    background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);
    display: flex;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.featured-card:hover .featured-overlay {
    opacity: 1;
}

.featured-quick-add {
    width: 100%;
    height: 10%;
    background: #b8925a;
    color: var(--white);
    border: none;
    cursor: pointer;
    transition: var(--transition);
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.featured-quick-add:hover {
    transform: scale(1);
    background: #b8925a;
}

.featured-quick-add:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

.featured-info {
    padding: 10px 12px;
}

.featured-info h4 {
    font-size: 13px;
    font-weight: 600;
    color: var(--primary-dark);
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.featured-price {
    font-size: 13px;
    font-weight: 700;
    color: var(--gold);
}

/* Products Section */
.products-section {
    padding: 0 20px 20px;
}

/* Product Card - Enhanced */
.product-card {
    background: var(--white);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-light);
    transition: var(--transition);
    border: 1px solid rgba(201, 169, 110, 0.06);
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow);
}

.product-card-inner {
    cursor: pointer;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.product-image-wrapper {
    position: relative;
    overflow: hidden;
}

.product-image {
    width: 100%;
    aspect-ratio: 1/1;
    background: var(--beige);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-image .placeholder-icon {
    font-size: 40px;
    color: var(--gold-light);
    opacity: 0.5;
}

.product-image .unavailable-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(26, 16, 8, 0.85);
    color: var(--white);
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.product-image .unavailable-badge i {
    font-size: 12px;
}

.product-image .featured-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: var(--gold);
    color: var(--white);
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    box-shadow: 0 2px 12px rgba(201, 169, 110, 0.4);
}

.product-info {
    padding: 12px 14px 14px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-name {
    font-size: 15px;
    font-weight: 600;
    color: var(--primary-dark);
    line-height: 1.3;
}

.product-name-en {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 400;
    font-family: var(--font-latin);
    direction: ltr;
    display: block;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 4px 0 6px;
}

.product-price {
    font-size: 16px;
    font-weight: 700;
    color: var(--primary-brown);
}

.product-price small {
    font-size: 11px;
    font-weight: 400;
    color: var(--text-muted);
}

.product-calories {
    font-size: 12px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 4px;
}

.product-calories i {
    font-size: 12px;
    color: #e74c3c;
}

.product-description {
    font-size: 12px;
    color: var(--text-muted);
    line-height: 1.5;
    margin: 4px 0 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin: 4px 0 8px;
}

.product-tags .tag {
    padding: 2px 10px;
    background: var(--beige);
    border-radius: 50px;
    font-size: 10px;
    color: var(--text-muted);
    font-weight: 500;
}

.product-tags .tag-more {
    background: var(--gold-light);
    color: var(--gold);
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid rgba(201, 169, 110, 0.08);
}

.product-category {
    font-size: 11px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 4px;
}

.product-category i {
    font-size: 11px;
}

.btn-add-cart {
    padding: 6px 14px;
    background: var(--primary-brown);
    color: var(--white);
    border: none;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    font-family: var(--font-arabic);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 4px;
}

.btn-add-cart:hover {
    background: var(--gold);
    transform: scale(1.05);
}

.btn-add-cart:disabled {
    background: #ccc;
    cursor: not-allowed;
    opacity: 0.6;
    transform: none;
}

.btn-add-cart:disabled:hover {
    background: #ccc;
    transform: none;
}

.btn-add-cart i {
    font-size: 11px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 48px;
    color: var(--gold-light);
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 18px;
    color: var(--primary-dark);
    margin-bottom: 4px;
}

.empty-state p {
    color: var(--text-muted);
    font-size: 14px;
}

/* Filter Modal */
.filter-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(26, 16, 8, 0.5);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 2000;
    display: none;
    align-items: flex-end;
    justify-content: center;
}

.filter-modal.active {
    display: flex;
}

.filter-modal-content {
    background: var(--white);
    border-radius: var(--radius) var(--radius) 0 0;
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    animation: slideUp 0.3s ease;
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px 16px;
    border-bottom: 1px solid var(--beige);
}

.filter-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary-dark);
    font-family: var(--font-display);
}

.filter-close {
    background: none;
    border: none;
    font-size: 22px;
    color: var(--text-muted);
    cursor: pointer;
    transition: var(--transition);
    padding: 4px;
}

.filter-close:hover {
    color: #e74c3c;
    transform: rotate(90deg);
}

.filter-body {
    padding: 20px 24px;
    overflow-y: auto;
    flex: 1;
}

.filter-group {
    margin-bottom: 20px;
}

.filter-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--primary-dark);
    margin-bottom: 8px;
}

.filter-options {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.filter-option {
    padding: 6px 16px;
    background: var(--beige);
    border: 2px solid transparent;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 500;
    font-family: var(--font-arabic);
    color: var(--text-muted);
    cursor: pointer;
    transition: var(--transition);
}

.filter-option:hover {
    background: var(--gold-light);
}

.filter-option.active {
    background: var(--gold);
    color: var(--white);
    border-color: var(--gold);
}

.filter-price-range {
    padding: 4px 0;
}

.filter-price-range input[type="range"] {
    width: 100%;
    height: 4px;
    -webkit-appearance: none;
    background: var(--beige);
    border-radius: 10px;
    outline: none;
}

.filter-price-range input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: var(--gold);
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(201, 169, 110, 0.3);
}

.price-labels {
    display: flex;
    justify-content: space-between;
    margin-top: 6px;
    font-size: 12px;
    color: var(--text-muted);
}

.filter-footer {
    display: flex;
    gap: 12px;
    padding: 16px 24px 24px;
    border-top: 1px solid var(--beige);
}

.filter-footer button {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    font-family: var(--font-arabic);
    cursor: pointer;
    transition: var(--transition);
}

.filter-reset {
    background: var(--beige);
    color: var(--text-muted);
}

.filter-reset:hover {
    background: #e8ddd0;
}

.filter-apply {
    background: var(--gold);
    color: var(--white);
}

.filter-apply:hover {
    background: #b8925a;
}

/* ========================================
   Responsive
   ======================================== */

@media (min-width: 768px) {
    .menu-hero {
        padding: 60px 40px 48px;
    }
    
    .menu-toolbar {
        padding: 20px 40px;
    }
    
    .categories-scroll {
        padding: 16px 40px;
    }
    
    .featured-section {
        padding: 24px 40px 16px;
    }
    
    .featured-card {
        width: 180px;
    }
    
    .featured-image {
        width: 180px;
        height: 180px;
    }
    
    .products-section {
        padding: 0 40px 24px;
    }
    
    .products-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .filter-modal-content {
        border-radius: var(--radius);
        margin-bottom: 40px;
    }
    
    .filter-modal {
        align-items: center;
    }
}

@media (min-width: 1024px) {
    .menu-hero {
        padding: 80px 60px 64px;
    }
    
    .menu-hero-title {
        font-size: 48px;
    }
    
    .products-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }
    
    .featured-scroll {
        gap: 20px;
    }
    
    .featured-card {
        width: 200px;
    }
    
    .featured-image {
        width: 200px;
        height: 200px;
    }
}

@media (max-width: 480px) {
    .products-grid {
        gap: 12px;
    }
    
    .product-info {
        padding: 10px 12px 12px;
    }
    
    .product-name {
        font-size: 13px;
    }
    
    .product-price {
        font-size: 14px;
    }
    
    .btn-add-cart {
        font-size: 11px;
        padding: 4px 10px;
    }
    
    .featured-card {
        width: 120px;
    }
    
    .featured-image {
        width: 120px;
        height: 120px;
    }
    
    .featured-info h4 {
        font-size: 12px;
    }
    
    .featured-price {
        font-size: 12px;
    }
    
    .filter-trigger span {
        display: none;
    }
}
</style>

<script>
// ========================================
// Menu Page JavaScript
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Category filtering
    const categoryBtns = document.querySelectorAll('.category-btn');
    const productCards = document.querySelectorAll('.product-card');
    const searchInput = document.getElementById('searchInput');
    const searchClear = document.getElementById('searchClear');
    const emptyState = document.getElementById('emptyState');
    const productCount = document.getElementById('productCount');
    const filterToggle = document.getElementById('filterToggle');
    const filterModal = document.getElementById('filterModal');
    const filterOptions = document.querySelectorAll('.filter-option');
    const priceRange = document.getElementById('priceRange');
    const priceRangeLabel = document.getElementById('priceRangeLabel');
    
    let currentCategory = 'all';
    let currentSearch = '';
    let currentStatus = 'all';
    let currentPrice = 100;
    
    // Category filter
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            categoryBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.dataset.category;
            filterProducts();
        });
    });
    
    // Search
    searchInput.addEventListener('input', function() {
        currentSearch = this.value.trim();
        searchClear.style.display = currentSearch ? 'block' : 'none';
        filterProducts();
    });
    
    searchClear.addEventListener('click', function() {
        searchInput.value = '';
        currentSearch = '';
        this.style.display = 'none';
        filterProducts();
        searchInput.focus();
    });
    
    // Filter modal
    filterToggle.addEventListener('click', function() {
        filterModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
    
    function closeFilterModal() {
        filterModal.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    window.closeFilterModal = closeFilterModal;
    
    filterModal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeFilterModal();
        }
    });
    
    // Filter options
    filterOptions.forEach(opt => {
        opt.addEventListener('click', function() {
            const parent = this.closest('.filter-options');
            parent.querySelectorAll('.filter-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Price range
    priceRange.addEventListener('input', function() {
        const val = this.value;
        priceRangeLabel.textContent = val >= 100 ? '100+ SAR' : val + ' SAR';
        currentPrice = parseInt(val);
    });
    
    window.applyFilters = function() {
        const statusOpt = document.querySelector('.filter-option[data-status].active');
        currentStatus = statusOpt ? statusOpt.dataset.status : 'all';
        
        const categoryOpt = document.querySelector('.filter-option[data-filter].active');
        if (categoryOpt && categoryOpt.dataset.filter !== 'all') {
            currentCategory = categoryOpt.dataset.filter;
            // Update category buttons
            categoryBtns.forEach(b => {
                b.classList.toggle('active', b.dataset.category === currentCategory);
            });
        }
        
        filterProducts();
        closeFilterModal();
    };
    
    window.resetFilters = function() {
        // Reset category
        currentCategory = 'all';
        categoryBtns.forEach(b => b.classList.toggle('active', b.dataset.category === 'all'));
        
        // Reset status
        currentStatus = 'all';
        document.querySelectorAll('.filter-option[data-status]').forEach(o => {
            o.classList.toggle('active', o.dataset.status === 'all');
        });
        
        // Reset price
        priceRange.value = 100;
        priceRangeLabel.textContent = '100+ SAR';
        currentPrice = 100;
        
        filterProducts();
        closeFilterModal();
    };
    
    // Main filter function
    function filterProducts() {
        let visibleCount = 0;
        const searchLower = currentSearch.toLowerCase();
        
        productCards.forEach(card => {
            const categoryId = card.dataset.categoryId;
            const productName = (card.dataset.productName || '').toLowerCase();
            const isAvailable = !card.querySelector('.unavailable-badge');
            
            let matchCategory = currentCategory === 'all' || categoryId == currentCategory;
            let matchSearch = !searchLower || productName.includes(searchLower);
            let matchStatus = currentStatus === 'all' || 
                (currentStatus === 'available' && isAvailable) ||
                (currentStatus === 'unavailable' && !isAvailable);
            
            // Price filtering (simplified - based on product price)
            let matchPrice = true;
            const priceEl = card.querySelector('.product-price');
            if (priceEl) {
                const priceText = priceEl.textContent.trim();
                const price = parseFloat(priceText);
                if (!isNaN(price) && currentPrice < 100) {
                    matchPrice = price <= currentPrice;
                }
            }
            
            const show = matchCategory && matchSearch && matchStatus && matchPrice;
            card.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });
        
        // Update count
        if (productCount) {
            productCount.textContent = visibleCount + ' منتج';
        }
        
        // Show empty state
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (filterModal.classList.contains('active')) {
                closeFilterModal();
            }
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
    });
});

// Override addToCart to show animation
const originalAddToCart = window.addToCart;
window.addToCart = function(productId, name, price, quantity) {
    // Call original function
    if (typeof originalAddToCart === 'function') {
        originalAddToCart(productId, name, price, quantity);
    }
    
    // Find the product card and add animation
    const cards = document.querySelectorAll('.product-card');
    cards.forEach(card => {
        const btn = card.querySelector('.btn-add-cart');
        if (btn) {
            btn.classList.add('added');
            setTimeout(() => {
                btn.classList.remove('added');
            }, 500);
        }
    });
};
</script>

<?php include 'includes/footer.php'; ?>