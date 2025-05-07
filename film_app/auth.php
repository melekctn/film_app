<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
if (!isset($_SESSION["user_id"])) {
    // Eğer istek bir AJAX isteğiyse, JSON hata döndür
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "error" => "Yetkisiz erişim."]);
        exit;
    } else {
        // Normal tarayıcı isteği ise login.php'ye yönlendir
        header("Location: login.php");
        exit;
    }
}
?>