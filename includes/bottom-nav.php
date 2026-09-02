<!-- Bottom Navigation -->
<nav class="bottom-nav" id="bottomNav">
    <a href="index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" data-page="home">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <span class="nav-label">الرئيسية</span>
    </a>
    <a href="menu.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' || basename($_SERVER['PHP_SELF']) == 'product.php' ? 'active' : ''; ?>" data-page="menu">
        <div class="nav-icon">
            <i class="fas fa-utensils"></i>
        </div>
        <span class="nav-label">المنيو</span>
    </a>
    <a href="cart.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : ''; ?>" data-page="cart">
        <div class="nav-icon">
            <i class="fas fa-shopping-bag"></i>
            <span class="badge" id="cartBadge">0</span>
        </div>
        <span class="nav-label">السلة</span>
    </a>
    <a href="about.php" class="nav-item" data-page="about">
        <div class="nav-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <span class="nav-label">عن يوفيا</span>
    </a>
</nav>