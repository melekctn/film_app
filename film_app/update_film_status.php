
<?php
require 'db.php';
session_start();

// Kullanıcı giriş yapmış mı kontrol
if (!isset($_SESSION['user_id'])) {
    echo "Önce giriş yapmalısınız.";
    exit;
}

// POST verileri geliyor mu kontrol
if (isset($_POST['film_id']) && isset($_POST['status'])) {
    $user_id = $_SESSION['user_id'];
    $film_id = intval($_POST['film_id']);
    $status = htmlspecialchars(trim($_POST['status']));
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;

    // Kullanıcı bu film için daha önce kayıt yapmış mı kontrol et
    $stmt = $conn->prepare("SELECT id FROM user_films WHERE user_id = ? AND film_id = ?");
    $stmt->bind_param("ii", $user_id, $film_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Zaten var, güncelle
        if ($rating !== null) {
            $update = $conn->prepare("UPDATE user_films SET status = ?, rating = ? WHERE user_id = ? AND film_id = ?");
            $update->bind_param("siii", $status, $rating, $user_id, $film_id);
        } else {
            $update = $conn->prepare("UPDATE user_films SET status = ? WHERE user_id = ? AND film_id = ?");
            $update->bind_param("sii", $status, $user_id, $film_id);
        }
        $update->execute();
    } else {
        // Yok, yeni kayıt ekle
        if ($rating !== null) {
            $insert = $conn->prepare("INSERT INTO user_films (user_id, film_id, status, rating) VALUES (?, ?, ?, ?)");
            $insert->bind_param("iisi", $user_id, $film_id, $status, $rating);
        } else {
            $insert = $conn->prepare("INSERT INTO user_films (user_id, film_id, status) VALUES (?, ?, ?)");
            $insert->bind_param("iis", $user_id, $film_id, $status);
        }
        $insert->execute();
    }

    echo "success";
} else {
    echo "Eksik veri!";
}
?>
