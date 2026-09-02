<?php
require_once '../config/database.php';
require_once 'auth_check.php';

// Handle toggle availability
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $stmt = $pdo->prepare("UPDATE products SET is_available = NOT is_available WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: products.php?msg=updated');
    exit();
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: products.php?msg=deleted');
    exit();
}

$products = $pdo->query("
    SELECT p.*, c.name_ar as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
")->fetchAll();

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المنتجات - يوفيا</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                <h1>المنتجات</h1>
                <a href="add-product.php" class="btn-add">
                    <i class="fas fa-plus"></i> إضافة منتج
                </a>
            </div>
            
            <?php if ($msg == 'updated'): ?>
                <div class="admin-alert success">تم تحديث حالة المنتج بنجاح</div>
            <?php elseif ($msg == 'deleted'): ?>
                <div class="admin-alert success">تم حذف المنتج بنجاح</div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>السعر</th>
                            <th>التصنيف</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td><?php echo $p['id']; ?></td>
                                    <td>
                                        <?php if ($p['image']): ?>
                                            <img src="../assets/images/<?php echo htmlspecialchars($p['image']); ?>" width="40" height="40" style="border-radius:8px;object-fit:cover;">
                                        <?php else: ?>
                                            <i class="fas fa-coffee" style="font-size:24px;color:#ccc;"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($p['name_ar']); ?></strong><br>
                                        <small style="color:#6b5a4a;"><?php echo htmlspecialchars($p['name']); ?></small>
                                    </td>
                                    <td><?php echo number_format($p['price'], 2); ?> SAR</td>
                                    <td><?php echo htmlspecialchars($p['category_name'] ?? 'بدون تصنيف'); ?></td>
                                    <td>
                                        <a href="?toggle=<?php echo $p['id']; ?>" class="status-badge <?php echo $p['is_available'] ? 'available' : 'unavailable'; ?>">
                                            <i class="fas <?php echo $p['is_available'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                            <?php echo $p['is_available'] ? 'متوفر' : 'غير متوفر'; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="edit-product.php?id=<?php echo $p['id']; ?>" class="action-link edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $p['id']; ?>" class="action-link delete" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">لا توجد منتجات</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>