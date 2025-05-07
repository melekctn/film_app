<link rel="stylesheet" href="home.css">
<?php
require 'db.php';
session_start();

// Giriş yapılmadıysa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Arama işlemi
$search = "";
if (isset($_GET['q'])) {
    $search = trim($_GET['q']);
}

// Film durumunu ve puanını güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['film_id'], $_POST['status'])) {
    $film_id = intval($_POST['film_id']);
    $status = trim($_POST['status']);
    $user_id = $_SESSION['user_id'];
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;

    $check = $conn->prepare("SELECT id FROM user_films WHERE user_id = ? AND film_id = ?");
    $check->bind_param("ii", $user_id, $film_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        if ($rating !== null) {
            $update = $conn->prepare("UPDATE user_films SET status = ?, rating = ? WHERE user_id = ? AND film_id = ?");
            $update->bind_param("siii", $status, $rating, $user_id, $film_id);
        } else {
            $update = $conn->prepare("UPDATE user_films SET status = ? WHERE user_id = ? AND film_id = ?");
            $update->bind_param("sii", $status, $user_id, $film_id);
        }
        $update->execute();
    } else {
        if ($rating !== null) {
            $insert = $conn->prepare("INSERT INTO user_films (user_id, film_id, status, rating) VALUES (?, ?, ?, ?)");
            $insert->bind_param("iisi", $user_id, $film_id, $status, $rating);
        } else {
            $insert = $conn->prepare("INSERT INTO user_films (user_id, film_id, status) VALUES (?, ?, ?)");
            $insert->bind_param("iis", $user_id, $film_id, $status);
        }
        $insert->execute();
    }
}

// Filmleri getir
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT * FROM films WHERE title LIKE CONCAT('%', ?, '%') ORDER BY created_at DESC");
    $stmt->bind_param("s", $search);
} else {
    $stmt = $conn->prepare("SELECT * FROM films ORDER BY created_at DESC");
}
$stmt->execute();
$films = $stmt->get_result();

// Kullanıcının film durumlarını ve puanlarını getir
$user_id = $_SESSION['user_id'];
$user_films_query = $conn->prepare("SELECT film_id, status, rating FROM user_films WHERE user_id = ?");
$user_films_query->bind_param("i", $user_id);
$user_films_query->execute();
$user_films_result = $user_films_query->get_result();

$user_films = [];
while ($row = $user_films_result->fetch_assoc()) {
    $user_films[$row['film_id']] = [
        'status' => $row['status'],
        'rating' => $row['rating']
    ];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Film Takip</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>🎬 Hoş geldin <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
<a href="logout.php" class="logout-button">Çıkış Yap</a>
 
    <form method="get" action="">
    <div class="search-container">
<input type="text" placeholder="Film ara...">
<br>
<button>Ara</button>
</div>
</form>
 
    <h3>📚 Filmler:</h3>
 
    <?php if ($films->num_rows > 0): ?>
<ul>
<?php while ($film = $films->fetch_assoc()): ?>
<li>
<h4><?php echo htmlspecialchars($film['title']); ?></h4>
<p><?php echo htmlspecialchars($film['description']); ?></p>
 
                    <form method="post" style="display:inline;">
<input type="hidden" name="film_id" value="<?php echo $film['id']; ?>">
<input type="hidden" name="status" value="Devam Ediliyor">
<button type="submit">Devam Ediliyor</button>
</form>
 
                    <form method="post" style="display:inline;">
<input type="hidden" name="film_id" value="<?php echo $film['id']; ?>">
<input type="hidden" name="status" value="İzlenecek">
<button type="submit">İzlenecek</button>
</form>
 
                    <form method="post" style="display:inline;">
<input type="hidden" name="film_id" value="<?php echo $film['id']; ?>">
<input type="hidden" name="status" value="İzlendi">
<button type="submit">İzlendi</button>
</form>
 
                    <div class="film-status">
<form method="post">
<input type="hidden" name="film_id" value="<?php echo $film['id']; ?>">
<input type="hidden" name="status" value="<?php echo isset($user_films[$film['id']]['status']) ? htmlspecialchars($user_films[$film['id']]['status']) : 'İzlendi'; ?>">
<label>Puan Ver:</label>
<input type="number" name="rating" min="1" max="10" value="<?php echo isset($user_films[$film['id']]['rating']) ? $user_films[$film['id']]['rating'] : ''; ?>">
<button type="submit">Kaydet</button>
</form>
</div>
</li>
<?php endwhile; ?>
</ul>
<?php else: ?>
<p>Hiç film bulunamadı.</p>
<?php endif; ?>
 
    <hr>

<div class="chatbot-container">
<div class="chatbot-title">🤖Film asistanı ile sohbet et</div>
<textarea id="chatInput" class="chatbot-input" placeholder="Mesajınızı yazın..."></textarea>
<button class="chatbot-send-btn" onclick="sendMessage()">Gönder</button>


</div>

 
    <div id="chatReply" style="margin-top: 20px; padding: 10px; background: #f0f0f0; border: 1px solid #ccc;"></div>
 
    <script>

        function sendMessage() {

            const prompt = document.getElementById('chatInput').value;

            if (!prompt.trim()) {

                alert('Lütfen bir mesaj yazın.');

                return;

            }
 
            fetch('chat_home.php', {

                method: 'POST',

                headers: {

                    'Content-Type': 'application/x-www-form-urlencoded'

                },

                body: 'prompt=' + encodeURIComponent(prompt)

            })

            .then(response => response.json())

            .then(data => {

                if (data.reply) {

                    document.getElementById('chatReply').innerText = data.reply;

                } else {

                    document.getElementById('chatReply').innerText = 'Hata: ' + data.error;

                }

            })

            .catch(error => {

                document.getElementById('chatReply').innerText = 'Sunucu hatası.';

            });

        }
</script>
</body>
</html>

 
