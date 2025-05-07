<link rel="stylesheet" href="register.css">
<?php
require 'db.php';
session_start();
$error = "";
$success = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Tüm alanları doldurun.";
    } elseif ($password !== $confirm_password) {
        $error = "Şifreler eşleşmiyor.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = "Bu kullanıcı adı zaten kullanılıyor.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                if ($insert) {
                    $insert->bind_param("ss", $username, $hashedPassword);
                    if ($insert->execute()) {
                        $success = "Kayıt başarılı! Şimdi giriş yapabilirsiniz.";
                    } else {
                        $error = "Kayıt başarısız oldu.";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Kayıt Ol</title>
</head>
<body>
<?php if (!empty($error)): ?>
<p class="error"><?php echo $error; ?></p>
<?php elseif (!empty($success)): ?>
<p class="success"><?php echo $success; ?></p>
<?php endif; ?>
<form method="post" action="">
<h2>Kayıt Ol</h2>
<label for="username">Kullanıcı Adı:</label>
<input type="text" name="username" required><br><br>
<label for="password">Şifre:</label>
<input type="password" name="password" required><br><br>
<label for="confirm_password">Şifre Tekrar:</label>
<input type="password" name="confirm_password" required><br><br>
<button type="submit">Kayıt Ol</button>
<p><a href="login.php">Zaten üye misiniz? Giriş yapın</a></p>
</form>
</body>
</html>