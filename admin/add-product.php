<?php
require_once '../config/database.php';
require_once 'auth_check.php';

// Fetch categories for dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY display_order ASC")->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $name_ar = trim($_POST['name_ar'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $description_ar = trim($_POST['description_ar'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $calories = (int)($_POST['calories'] ?? 0);
    $origin = trim($_POST['origin'] ?? '');
    $origin_ar = trim($_POST['origin_ar'] ?? '');
    $process_method = trim($_POST['process_method'] ?? '');
    $process_method_ar = trim($_POST['process_method_ar'] ?? '');
    $roast_level = trim($_POST['roast_level'] ?? '');
    $roast_level_ar = trim($_POST['roast_level_ar'] ?? '');
    $caffeine_level = trim($_POST['caffeine_level'] ?? '');
    $caffeine_level_ar = trim($_POST['caffeine_level_ar'] ?? '');
    $flavor_notes = trim($_POST['flavor_notes'] ?? '');
    $flavor_notes_ar = trim($_POST['flavor_notes_ar'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $ingredients_ar = trim($_POST['ingredients_ar'] ?? '');
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $display_order = (int)($_POST['display_order'] ?? 0);
    $tags = $_POST['tags'] ?? [];
    $tags_ar = $_POST['tags_ar'] ?? [];
    
    // Validate
    if (empty($name) || empty($name_ar) || $category_id == 0 || $price <= 0) {
        $error = 'يرجى ملء جميع الحقول المطلوبة';
    } else {
        try {
            // Handle image upload
            $image_name = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, $allowed)) {
                    $image_name = time() . '_' . uniqid() . '.' . $ext;
                    $upload_path = '../assets/images/' . $image_name;
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        $error = 'فشل رفع الصورة';
                    }
                } else {
                    $error = 'نوع الصورة غير مدعوم';
                }
            }
            
            if (empty($error)) {
                $stmt = $pdo->prepare("
                    INSERT INTO products (
                        category_id, name, name_ar, description, description_ar,
                        price, calories, image, origin, origin_ar,
                        process_method, process_method_ar, roast_level, roast_level_ar,
                        caffeine_level, caffeine_level_ar, flavor_notes, flavor_notes_ar,
                        ingredients, ingredients_ar, is_available, is_featured, display_order
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $category_id, $name, $name_ar, $description, $description_ar,
                    $price, $calories, $image_name, $origin, $origin_ar,
                    $process_method, $process_method_ar, $roast_level, $roast_level_ar,
                    $caffeine_level, $caffeine_level_ar, $flavor_notes, $flavor_notes_ar,
                    $ingredients, $ingredients_ar, $is_available, $is_featured, $display_order
                ]);
                
                $product_id = $pdo->lastInsertId();
                
                // Add tags
                if ($product_id && !empty($tags)) {
                    $tagStmt = $pdo->prepare("INSERT INTO product_tags (product_id, tag, tag_ar) VALUES (?, ?, ?)");
                    foreach ($tags as $index => $tag) {
                        if (!empty($tag) && !empty($tags_ar[$index])) {
                            $tagStmt->execute([$product_id, $tag, $tags_ar[$index]]);
                        }
                    }
                }
                
                $success = 'تم إضافة المنتج بنجاح';
            }
        } catch (PDOException $e) {
            $error = 'حدث خطأ في قاعدة البيانات';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة منتج - يوفيا</title>
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
                <h1>إضافة منتج جديد</h1>
                <a href="products.php" class="btn-add" style="background:#6b5a4a;">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="admin-alert error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="admin-alert success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <div class="form-grid">
                    <div class="form-section">
                        <h3>المعلومات الأساسية</h3>
                        
                        <div class="form-group">
                            <label>التصنيف <span class="required">*</span></label>
                            <select name="category_id" required>
                                <option value="">اختر التصنيف</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name_ar']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>اسم المنتج (عربي) <span class="required">*</span></label>
                            <input type="text" name="name_ar" required>
                        </div>
                        
                        <div class="form-group">
                            <label>اسم المنتج (إنجليزي) <span class="required">*</span></label>
                            <input type="text" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label>السعر (SAR) <span class="required">*</span></label>
                            <input type="number" name="price" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label>السعرات الحرارية</label>
                            <input type="number" name="calories" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label>الوصف (عربي)</label>
                            <textarea name="description_ar" rows="3"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>الوصف (إنجليزي)</label>
                            <textarea name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>الصورة</label>
                            <input type="file" name="image" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>معلومات القهوة (اختياري)</h3>
                        
                        <div class="form-group">
                            <label>المنشأ (عربي)</label>
                            <input type="text" name="origin_ar">
                        </div>
                        
                        <div class="form-group">
                            <label>المنشأ (إنجليزي)</label>
                            <input type="text" name="origin">
                        </div>
                        
                        <div class="form-group">
                            <label>المعالجة (عربي)</label>
                            <input type="text" name="process_method_ar">
                        </div>
                        
                        <div class="form-group">
                            <label>المعالجة (إنجليزي)</label>
                            <input type="text" name="process_method">
                        </div>
                        
                        <div class="form-group">
                            <label>التحميص (عربي)</label>
                            <input type="text" name="roast_level_ar">
                        </div>
                        
                        <div class="form-group">
                            <label>التحميص (إنجليزي)</label>
                            <input type="text" name="roast_level">
                        </div>
                        
                        <div class="form-group">
                            <label>مستوى الكافيين (عربي)</label>
                            <input type="text" name="caffeine_level_ar">
                        </div>
                        
                        <div class="form-group">
                            <label>مستوى الكافيين (إنجليزي)</label>
                            <input type="text" name="caffeine_level">
                        </div>
                        
                        <div class="form-group">
                            <label>الإيحاءات (عربي)</label>
                            <input type="text" name="flavor_notes_ar">
                        </div>
                        
                        <div class="form-group">
                            <label>الإيحاءات (إنجليزي)</label>
                            <input type="text" name="flavor_notes">
                        </div>
                        
                        <div class="form-group">
                            <label>المكونات (عربي)</label>
                            <textarea name="ingredients_ar" rows="2"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>المكونات (إنجليزي)</label>
                            <textarea name="ingredients" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>الإعدادات</h3>
                        
                        <div class="form-group">
                            <label>ترتيب العرض</label>
                            <input type="number" name="display_order" value="0">
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" name="is_available" checked>
                                متوفر
                            </label>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" name="is_featured">
                                مميز
                            </label>
                        </div>
                        
                        <h3 style="margin-top:20px;">الوسوم</h3>
                        <p style="color:#6b5a4a;font-size:13px;">أضف وسوم للمنتج (مثل: حلو، بارد، Specialty)</p>
                        
                        <div class="tag-inputs">
                            <div class="tag-row">
                                <input type="text" name="tags[]" placeholder="وسم (عربي)">
                                <input type="text" name="tags_ar[]" placeholder="وسم (إنجليزي)">
                            </div>
                            <div class="tag-row">
                                <input type="text" name="tags[]" placeholder="وسم (عربي)">
                                <input type="text" name="tags_ar[]" placeholder="وسم (إنجليزي)">
                            </div>
                            <div class="tag-row">
                                <input type="text" name="tags[]" placeholder="وسم (عربي)">
                                <input type="text" name="tags_ar[]" placeholder="وسم (إنجليزي)">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> حفظ المنتج
                    </button>
                </div>
            </form>
        </main>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>