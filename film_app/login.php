<link rel="stylesheet" href="login.css">
<?php

require 'db.php';

session_start();

// Eğer kullanıcı zaten giriş yaptıysa tekrar login sayfasını göstermeyelim

if (isset($_SESSION['user_id'])) {

    // Admin mi kontrol et

    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {

        header('Location: index.php');

        exit;

    } else {

        header('Location: home.php');

        exit;

    }

}

?>
<?php

require 'db.php';

if (session_status() === PHP_SESSION_NONE) {

    session_start();

}

if (isset($_SESSION['user_id'])) {

    header('Location: home.php');

    exit;

}

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);

    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");

    $stmt->bind_param("s", $username);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];

            $_SESSION['username'] = $user['username'];

            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {

                header('Location: index.php');

                exit;

            } else {

                header('Location: home.php');

                exit;

            }

        } else {

            $error = "Hatalı şifre!";

        }

    } else {

        $error = "Kullanıcı bulunamadı!";

    }

}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Giriş Yap</title>
</head>
<body>
<?php if (!empty($error)): ?>
<p style="color:red;"> <?php echo htmlspecialchars($error); ?> </p>
<?php endif; ?>
<form method="post" action="">
<h2>Giriş Yap</h2>
<label for="username">Kullanıcı Adı:</label>
<input type="text" name="username" required><br><br>
<label for="password">Şifre:</label>
<input type="password" name="password" required><br>
<button type="submit">Giriş Yap</button>
<p><a href="register.php">Hesabınız yok mu? Kayıt olun</a></p>
</form>

</body>
</html>

 