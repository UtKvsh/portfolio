<?php
session_start();
require_once 'includes/db.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = 'Lütfen kullanıcı adı ve şifre girin.';
    } else {
        // Fetch user from DB
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();
        
        // Check password
        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, set session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            
            // Set cookie if "Remember Me" is checked (Optional feature)
            if (isset($_POST['remember_me'])) {
                setcookie('admin_auth', $user['username'], time() + (86400 * 30), "/"); // 30 days
            }
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Hatalı kullanıcı adı veya şifre.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Girişi - Portfolyo</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-color);
        }
        .login-card {
            background: var(--surface-color);
            padding: 2.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            width: 100%;
            max-width: 400px;
            border: 1px solid var(--border-color);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2>Yönetici Girişi</h2>
                <p>Hoş geldiniz! Lütfen panele giriş yapın.</p>
            </div>
            
            <?php if ($error): ?>
                <div style="color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 10px; border-radius: 5px; margin-bottom: 1rem; text-align: center;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" id="remember_me" name="remember_me" style="width: auto;">
                    <label for="remember_me" style="margin: 0; font-weight: normal;">Beni Hatırla</label>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Giriş Yap</button>
            </form>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="index.php" style="color: var(--primary-color); font-size: 0.9rem;">&larr; Siteye Dön</a>
            </div>
        </div>
    </div>
</body>
</html>
