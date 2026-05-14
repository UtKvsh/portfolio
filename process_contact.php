<?php
// process_contact.php
require_once 'includes/db.php';

// Javascript'e düzgün bir JSON yanıtı vereceğimizi söylüyoruz
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Lütfen tüm alanları doldurun.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz e-posta adresi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Mesajınız başarıyla gönderildi! En kısa sürede döneceğim.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sistemsel bir hata oluştu.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Kayıt başarısız: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek türü.']);
}
?>