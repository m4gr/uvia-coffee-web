<?php
require_once '../config/database.php';
require_once '../config/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'يرجى إدخال اسم المستخدم وكلمة المرور';
    } else {
        // Debug - check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                header('Location: index.php');
                exit();
            } else {
                $error = 'كلمة المرور غير صحيحة';
            }
        } else {
            $error = 'اسم المستخدم غير موجود';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - لوحة تحكم يوفيا</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background: #f5efe6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            direction: rtl;
            padding: 20px;
        }
        .login-container {
            background: #fff;
            border-radius: 20px;
            padding: 40px 32px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(26,16,8,0.12);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1008;
        }
        .login-logo h1 span {
            color: #c9a96e;
        }
        .login-logo p {
            color: #6b5a4a;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1a1008;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e8e0d8;
            border-radius: 12px;
            font-size: 16px;
            font-family: inherit;
            transition: border-color 0.3s;
            background: #faf8f6;
        }
        .form-group input:focus {
            outline: none;
            border-color: #c9a96e;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #c9a96e;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #b8925a;
        }
        .error {
            background: #fee;
            color: #c0392b;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            border-right: 4px solid #c0392b;
        }
        .debug-info {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 8px;
            font-size: 12px;
            color: #666;
            margin-top: 10px;
            direction: ltr;
            text-align: left;
        }
        .admin-link {
            text-align: center;
            margin-top: 16px;
            font-size: 13px;
            color: #6b5a4a;
        }
        .admin-link a {
            color: #c9a96e;
            text-decoration: none;
        }
        .admin-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <h1>يوفيا <span>|</span> UVIA</h1>
            <p>لوحة التحكم</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>اسم المستخدم</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
            </button>
        </form>
        
        <!-- Debug Info (Remove in production) -->
        
        
        <div class="admin-link">
            <a href="../index.php"><i class="fas fa-arrow-right"></i> العودة للموقع</a>
        </div>
    </div>
</body>
</html>