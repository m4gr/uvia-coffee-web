<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <h2>يوفيا <span>|</span> UVIA</h2>
        <span class="brand-sub">لوحة التحكم</span>
    </div>
    
    <!-- Mobile Menu Toggle Button -->
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
        <i class="fas fa-bars"></i>
    </button>
    
    <nav class="sidebar-nav" id="sidebarNav">
        <a href="index.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-pie"></i>
            <span>لوحة التحكم</span>
        </a>
        <a href="products.php" class="sidebar-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['products.php', 'add-product.php', 'edit-product.php']) ? 'active' : ''; ?>">
            <i class="fas fa-utensils"></i>
            <span>المنتجات</span>
        </a>
        <a href="categories.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
            <i class="fas fa-tags"></i>
            <span>التصنيفات</span>
        </a>
        <a href="../index.php" class="sidebar-link" target="_blank">
            <i class="fas fa-external-link-alt"></i>
            <span>زيارة الموقع</span>
        </a>
        <a href="logout.php" class="sidebar-link logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>تسجيل الخروج</span>
        </a>
    </nav>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('menuToggle');
    const sidebarNav = document.getElementById('sidebarNav');
    
    if (toggleBtn && sidebarNav) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebarNav.classList.toggle('open');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!sidebarNav.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebarNav.classList.remove('open');
            }
        });
        
        // Close menu on link click (mobile)
        sidebarNav.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    sidebarNav.classList.remove('open');
                }
            });
        });
    }
});
</script>