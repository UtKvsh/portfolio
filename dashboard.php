<?php
session_start();
require_once 'includes/db.php';

// Check Authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // If not logged in but cookie exists, log them in via cookie
    if (isset($_COOKIE['admin_auth'])) {
        // Very basic cookie check for homework purposes
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $_COOKIE['admin_auth'];
    } else {
        header("Location: login.php");
        exit;
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('admin_auth', '', time() - 3600, '/');
    header("Location: login.php");
    exit;
}

// Handle Add Project
$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_project') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    $tags = trim($_POST['tags']);
    
    if (!empty($title) && !empty($description)) {
        $stmt = $pdo->prepare("INSERT INTO projects (title, description, image_url, tags) VALUES (:title, :description, :image_url, :tags)");
        $stmt->execute(['title' => $title, 'description' => $description, 'image_url' => $image_url, 'tags' => $tags]);
        $msg = "Proje başarıyla eklendi!";
    } else {
        $msg = "Başlık ve Açıklama zorunludur.";
    }
}

// Handle Delete Project
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: dashboard.php");
    exit;
}

// Fetch Projects
$projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();
// Fetch Messages
$messages = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Paneli</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background-color: var(--surface-color);
            border-right: 1px solid var(--border-color);
            padding: 2rem;
        }
        .main-content {
            padding: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: var(--surface-color);
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        th {
            background-color: rgba(0,0,0,0.02);
            font-weight: 600;
        }
        [data-theme="dark"] th { background-color: rgba(255,255,255,0.05); }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2 class="logo" style="margin-bottom: 2rem;">Yönetici Paneli</h2>
            <p>Hoş geldin, <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>!</p>
            <ul style="margin-top: 2rem; display: flex; flex-direction: column; gap: 1rem;">
                <li><a href="#projects">Projeleri Yönet</a></li>
                <li><a href="#messages">Mesajları Görüntüle</a></li>
                <li><a href="index.php" target="_blank">Siteyi Görüntüle</a></li>
                <li><a href="?logout=1" style="color: #ef4444;">Çıkış Yap</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <?php if ($msg): ?>
                <div style="background: #10b981; color: white; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <section id="projects" style="padding-top: 0;">
                <h2>Yeni Proje Ekle</h2>
                <form action="dashboard.php" method="POST" style="background: var(--surface-color); padding: 1.5rem; border-radius: var(--radius-md); margin-top: 1rem; border: 1px solid var(--border-color);">
                    <input type="hidden" name="action" value="add_project">
                    <div class="form-group">
                        <label>Başlık</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Açıklama</label>
                        <textarea name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Görsel Linki</label>
                        <input type="text" name="image_url" placeholder="https://ornek.com/resim.jpg">
                    </div>
                    <div class="form-group">
                        <label>Etiketler (Virgülle ayırın)</label>
                        <input type="text" name="tags" placeholder="PHP, HTML, CSS">
                    </div>
                    <button type="submit" class="btn btn-primary">Projeyi Kaydet</button>
                </form>

                <h2 style="margin-top: 3rem;">Mevcut Projeler</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Başlık</th>
                            <th>Etiketler</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($projects as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['title']) ?></td>
                            <td><?= htmlspecialchars($p['tags'] ?? '') ?></td>
                            <td>
                                <a href="?delete=<?= $p['id'] ?>" style="color: #ef4444;" onclick="return confirm('Emin misiniz?')">Sil</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <section id="messages" style="margin-top: 4rem;">
                <h2>İletişim Mesajları</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>İsim</th>
                            <th>E-posta</th>
                            <th>Mesaj</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($messages as $m): ?>
                        <tr>
                            <td><?= date('Y-m-d H:i', strtotime($m['created_at'])) ?></td>
                            <td><?= htmlspecialchars($m['name']) ?></td>
                            <td><?= htmlspecialchars($m['email']) ?></td>
                            <td><?= htmlspecialchars($m['message']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
