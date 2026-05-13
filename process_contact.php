<?php
// process_contact.php
require_once 'includes/db.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate inputs
    $name = trim(htmlspecialchars($_POST['name'] ?? ''));
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $message = trim(htmlspecialchars($_POST['message'] ?? ''));
    
    // Basic backend validation
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = 'Tüm alanların doldurulması zorunludur.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Geçersiz e-posta formatı.';
    } elseif (strlen($message) < 10) {
        $response['message'] = 'Mesajınız en az 10 karakter olmalıdır.';
    } else {
        try {
            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)");
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':message', $message);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Mesajınız için teşekkürler! En kısa sürede size dönüş yapacağım.';
            } else {
                $response['message'] = 'Mesaj kaydedilirken veritabanı hatası oluştu.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Veritabanı hatası: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Geçersiz istek metodu.';
}

// Check if it's an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Return JSON response for AJAX
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    // If not AJAX (fallback), redirect back with JS alert
    echo "<script>
        alert('{$response['message']}');
        window.location.href = 'index.php#contact';
    </script>";
}
?>
