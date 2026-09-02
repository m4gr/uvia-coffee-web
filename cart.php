<?php
include 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-shopping-bag" style="color:var(--gold);margin-left:10px;"></i> سلتي</h1>
    <p>قائمتك الشخصية للمراجعة</p>
</div>

<div class="cart-page">
    <!-- Cart Items -->
    <div id="cartItems" class="cart-items">
        <!-- Rendered by JavaScript -->
    </div>
    
    <!-- Empty State -->
    <div id="cartEmpty" class="cart-empty">
        <i class="fas fa-shopping-bag"></i>
        <h3>سلتك فارغة</h3>
        <p>أضف بعض المنتجات من الكتالوج</p>
        <a href="menu.php" class="btn-secondary" style="margin-top:16px;">
            <i class="fas fa-utensils" style="font-size:25px;"></i> استكشف المنيو
        </a>
    </div>
    
    <!-- Cart Summary -->
    <div id="cartSummary" class="cart-summary" style="display:none;">
        <div class="total-row">
            <span>المجموع</span>
            <span class="total-price" id="cartTotal">0.00 <span style="font-size:14px;font-weight:400;">SAR</span></span>
        </div>
        
        <div class="cart-actions">
            <button class="btn-clear" onclick="clearCart()">
                <i class="fas fa-trash-alt"></i> مسح السلة
            </button>
        </div>
    </div>
    
    <!-- Note -->
    <div class="cart-note">
        <i class="fas fa-mug-hot"></i>
        هذه قائمتك، احتفظ بها لتعرف ماذا تطلب عند وصول النادل
    </div>
</div>

<?php include 'includes/footer.php'; ?>