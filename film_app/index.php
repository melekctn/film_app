<link rel="stylesheet" href="index.css">
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php'; 
include 'auth.php';
// Sadece admin erişebilsin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: home.php');
    exit;
}
$search = "";
$result = null;
if (isset($_GET['q'])) {
    $search = htmlspecialchars(trim($_GET['q']));
    $stmt = $conn->prepare("SELECT * FROM films WHERE title LIKE CONCAT('%', ?, '%') ORDER BY created_at DESC");
    if ($stmt) {
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result = $stmt->get_result();
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM films ORDER BY created_at DESC");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Film Takip</title>
<style>
        .alert {
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
</style>
</head>
<body>
<div class="top-bar">
<form method="get" action="">
<input type="text" name="q" placeholder="Film ara..." value="<?php echo htmlspecialchars($search); ?>">
<button type="submit">Ara</button>
</form>
<form action="exit.php" method="post">
<button type="submit">🚪 Çıkış Yap</button>
</form>
</div>
<p><a href="create.php">➕ Yeni Film Ekle</a></p>
<div id="message"></div>
<h3>📂 Kayıtlı Filmler:</h3>
<?php if ($result && $result->num_rows > 0): ?>
<ul>
<?php while ($film = $result->fetch_assoc()): ?>
<li id="film-<?php echo $film['id']; ?>">
<strong><?php echo htmlspecialchars($film['title']); ?></strong><br>
<?php echo nl2br(htmlspecialchars($film['description'])); ?><br>
<small>Eklenme tarihi: <?php echo $film['created_at']; ?></small><br>
<a href="update.php?id=<?php echo $film['id']; ?>">✏️ Düzenle</a> |
<a href="#" class="delete-btn" data-id="<?php echo $film['id']; ?>">🗑️ Sil</a>
</li>
<hr>
<?php endwhile; ?>
</ul>
<?php else: ?>
<p>Hiç film eklenmemiş.</p>
<?php endif; ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const deleteButtons = document.querySelectorAll(".delete-btn");
    const messageBox = document.getElementById("message");
    deleteButtons.forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const filmId = this.getAttribute("data-id");
            if (confirm("Bu filmi silmek istiyor musunuz?")) {
                fetch("delete.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "id=" + filmId
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const filmElement = document.getElementById("film-" + filmId);
                        if (filmElement) {
                            filmElement.remove();
                        }
                        messageBox.innerHTML = '<div class="alert alert-success">Film başarıyla silindi.</div>';
                    } else {
                        messageBox.innerHTML = '<div class="alert alert-error">Hata: ' + data.error + '</div>';
                    }
                })
                .catch(() => {
                    messageBox.innerHTML = '<div class="alert alert-error">Sunucu hatası oluştu.</div>';
                });
            }
        });
    });
});
</script>
</body>
</html>