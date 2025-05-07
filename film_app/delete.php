
<?php
header('Content-Type: application/json');
require 'db.php';
include 'auth.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "error" => "Yetkisiz erişim."]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id > 0) {
            // Önce user_films tablosundaki bağlantılı kayıtları sil
            $stmt1 = $conn->prepare("DELETE FROM user_films WHERE film_id = ?");
            $stmt1->bind_param("i", $id);
            $stmt1->execute();
            $stmt1->close();

            // Şimdi ana filmi sil
            $stmt2 = $conn->prepare("DELETE FROM films WHERE id = ?");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $stmt2->close();

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Geçersiz ID."]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Geçersiz istek yöntemi."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "Hata: " . $e->getMessage()]);
}
?>
