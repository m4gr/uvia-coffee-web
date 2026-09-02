<?php
require_once '../config/database.php';
require_once 'auth_check.php';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = trim($_POST['name'] ?? '');
        $name_ar = trim($_POST['name_ar'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fa-tag');
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($name) && !empty($name_ar)) {
            $stmt = $pdo->prepare("INSERT INTO categories (name, name_ar, icon, display_order) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $name_ar, $icon, $display_order]);
            header('Location: categories.php?msg=added');
            exit();
        }
    } elseif (isset($_POST['edit'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name'] ?? '');
        $name_ar = trim($_POST['name_ar'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fa-tag');
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($name) && !empty($name_ar) && $id > 0) {
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, name_ar = ?, icon = ?, display_order = ? WHERE id = ?");
            $stmt->execute([$name, $name_ar, $icon, $display_order, $id]);
            header('Location: categories.php?msg=updated');
            exit();
        }
    } elseif (isset($_POST['delete']) && is_numeric($_POST['delete'])) {
        $id = (int)$_POST['delete'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: categories.php?msg=deleted');
        exit();
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY display_order ASC")->fetchAll();
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التصنيفات - يوفيا</title>
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
                <h1>التصنيفات</h1>
            </div>
            
            <?php if ($msg == 'added'): ?>
                <div class="admin-alert success">تم إضافة التصنيف بنجاح</div>
            <?php elseif ($msg == 'updated'): ?>
                <div class="admin-alert success">تم تحديث التصنيف بنجاح</div>
            <?php elseif ($msg == 'deleted'): ?>
                <div class="admin-alert success">تم حذف التصنيف بنجاح</div>
            <?php endif; ?>
            
            <!-- Add Category Form -->
            <div class="admin-card" style="margin-bottom:24px;">
                <h3>إضافة تصنيف جديد</h3>
                <form method="POST" class="inline-form">
                    <input type="text" name="name" placeholder="الاسم (إنجليزي)" required>
                    <input type="text" name="name_ar" placeholder="الاسم (عربي)" required>
                    <input type="text" name="icon" placeholder="أيقونة (مثل: fa-mug-hot)" value="fa-tag">
                    <input type="number" name="display_order" placeholder="ترتيب" value="0">
                    <button type="submit" name="add" class="btn-submit">
                        <i class="fas fa-plus"></i> إضافة
                    </button>
                </form>
            </div>
            
            <!-- Categories List -->
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الأيقونة</th>
                            <th>الاسم (عربي)</th>
                            <th>الاسم (إنجليزي)</th>
                            <th>الترتيب</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($categories) > 0): ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?php echo $cat['id']; ?></td>
                                    <td><i class="fas <?php echo $cat['icon'] ?? 'fa-tag'; ?>"></i></td>
                                    <td><?php echo htmlspecialchars($cat['name_ar']); ?></td>
                                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td><?php echo $cat['display_order']; ?></td>
                                    <td>
                                        <button class="action-link edit" onclick="editCategory(<?php echo $cat['id']; ?>, '<?php echo htmlspecialchars($cat['name']); ?>', '<?php echo htmlspecialchars($cat['name_ar']); ?>', '<?php echo $cat['icon'] ?? 'fa-tag'; ?>', <?php echo $cat['display_order']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟')">
                                            <input type="hidden" name="delete" value="<?php echo $cat['id']; ?>">
                                            <button type="submit" class="action-link delete" style="background:none;border:none;cursor:pointer;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">لا توجد تصنيفات</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <!-- Edit Modal -->
    <div id="editModal" class="modal-overlay" style="display:none;">
        <div class="modal-content" style="max-width:500px;padding:24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <h3>تعديل التصنيف</h3>
                <button onclick="closeEditModal()" style="background:none;border:none;font-size:24px;cursor:pointer;">×</button>
            </div>
            <form method="POST">
                <input type="hidden" name="id" id="editId">
                <div class="form-group">
                    <label>الاسم (عربي)</label>
                    <input type="text" name="name_ar" id="editNameAr" required>
                </div>
                <div class="form-group">
                    <label>الاسم (إنجليزي)</label>
                    <input type="text" name="name" id="editName" required>
                </div>
                <div class="form-group">
                    <label>الأيقونة</label>
                    <input type="text" name="icon" id="editIcon" value="fa-tag">
                </div>
                <div class="form-group">
                    <label>ترتيب العرض</label>
                    <input type="number" name="display_order" id="editOrder" value="0">
                </div>
                <button type="submit" name="edit" class="btn-submit" style="width:100%;">
                    <i class="fas fa-save"></i> تحديث
                </button>
            </form>
        </div>
    </div>
    
    <style>
        .modal-overlay {
            position:fixed;top:0;left:0;right:0;bottom:0;
            background:rgba(0,0,0,0.5);
            display:none;align-items:center;justify-content:center;
            z-index:1000;
        }
        .modal-overlay.active { display:flex; }
        .modal-content { background:#fff; border-radius:16px; max-width:500px; width:90%; }
        .inline-form {
            display:flex; flex-wrap:wrap; gap:10px;
        }
        .inline-form input {
            flex:1; min-width:120px; padding:10px 14px;
            border:2px solid #e8e0d8; border-radius:10px;
            font-family:inherit; font-size:14px;
        }
        .inline-form input:focus { border-color:#c9a96e; outline:none; }
        .inline-form .btn-submit {
            padding:10px 20px; background:#c9a96e; color:#fff;
            border:none; border-radius:10px; font-weight:600;
            cursor:pointer; font-family:inherit;
        }
        .inline-form .btn-submit:hover { background:#b8925a; }
    </style>
    
    <script>
        function editCategory(id, name, nameAr, icon, order) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editNameAr').value = nameAr;
            document.getElementById('editIcon').value = icon || 'fa-tag';
            document.getElementById('editOrder').value = order || 0;
            document.getElementById('editModal').classList.add('active');
            document.getElementById('editModal').style.display = 'flex';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            document.getElementById('editModal').style.display = 'none';
        }
        
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
    </script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>