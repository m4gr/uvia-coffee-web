<?php
require_once '../config/database.php';
require_once 'auth_check.php';

// Get stats
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$availableProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE is_available = 1")->fetchColumn();
$unavailableProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE is_available = 0")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - يوفيا</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Noto+Naskh+Arabic:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1><i class="fas fa-chart-pie"></i> لوحة التحكم</h1>
                <div class="admin-user">
                    <span class="user-avatar"><?php echo mb_substr($_SESSION['admin_username'] ?? 'A', 0, 1); ?></span>
                    <span><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-coffee"></i></div>
                    <div class="stat-info">
                        <span class="stat-number"><?php echo $totalProducts; ?></span>
                        <span class="stat-label">إجمالي المنتجات</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="color:#27ae60;"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <span class="stat-number"><?php echo $availableProducts; ?></span>
                        <span class="stat-label">متوفرة</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="color:#e74c3c;"><i class="fas fa-times-circle"></i></div>
                    <div class="stat-info">
                        <span class="stat-number"><?php echo $unavailableProducts; ?></span>
                        <span class="stat-label">غير متوفرة</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="color:#3498db;"><i class="fas fa-tags"></i></div>
                    <div class="stat-info">
                        <span class="stat-number"><?php echo $totalCategories; ?></span>
                        <span class="stat-label">التصنيفات</span>
                    </div>
                </div>
            </div>
            
            <div class="admin-actions-top">
                <a href="products.php" class="btn-add" style="background:var(--primary-brown);">
                    <i class="fas fa-utensils"></i> إدارة المنتجات
                </a>
                <a href="categories.php" class="btn-add" style="background:var(--secondary-brown);">
                    <i class="fas fa-tags"></i> إدارة التصنيفات
                </a>
                <a href="add-product.php" class="btn-add">
                    <i class="fas fa-plus"></i> إضافة منتج جديد
                </a>
                <a href="../index.php" class="btn-add" style="background:var(--text-muted);" target="_blank">
                    <i class="fas fa-external-link-alt"></i> زيارة الموقع
                </a>
            </div>
            
            <!-- Recent Products Preview -->
            <div style="margin-top:24px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <h3 style="font-size:16px;font-weight:700;color:var(--primary-dark);font-family:var(--font-display);">
                        <i class="fas fa-clock" style="color:var(--gold);margin-left:8px;"></i>
                        أحدث المنتجات
                    </h3>
                    <a href="products.php" style="color:var(--gold);text-decoration:none;font-size:14px;font-weight:600;">
                        عرض الكل <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>السعر</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recent = $pdo->query("SELECT name_ar, price, is_available FROM products ORDER BY id DESC LIMIT 5")->fetchAll();
                            if (count($recent) > 0):
                                foreach ($recent as $p):
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['name_ar']); ?></td>
                                    <td><?php echo number_format($p['price'], 2); ?> SAR</td>
                                    <td>
                                        <span class="status-badge <?php echo $p['is_available'] ? 'available' : 'unavailable'; ?>">
                                            <i class="fas <?php echo $p['is_available'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                            <?php echo $p['is_available'] ? 'متوفر' : 'غير متوفر'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="3" class="text-center">لا توجد منتجات</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>