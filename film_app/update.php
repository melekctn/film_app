<link rel="stylesheet" href="update.css">
<?php require 'db.php'; include 'auth.php'; $id = isset($_GET['id']) ? intval($_GET['id']) : 0; $success = ""; $error = ""; $title = ""; $genre = ""; if ($_SERVER["REQUEST_METHOD"] === "POST") {     $title = isset($_POST["title"]) ? htmlspecialchars(trim($_POST["title"])) : "";    $genre = isset($_POST["genre"]) ? htmlspecialchars(trim($_POST["genre"])) : "";     if (empty($title)) {         $error = "Film adı boş bırakılamaz.";     } else {         $stmt = $conn->prepare("UPDATE films SET title = ?, description = ? WHERE id = ?");         if ($stmt) {             $stmt->bind_param("ssi", $title, $genre, $id);             if ($stmt->execute()) {                 $success = "Film başarıyla güncellendi.";             } else {                 $error = "Güncelleme sırasında hata oluştu: " . $stmt->error;             }             $stmt->close();         } else {             $error = "Sorgu hazırlanamadı.";         }     } } else {     $stmt = $conn->prepare("SELECT * FROM films WHERE id = ?");     $stmt->bind_param("i", $id);     $stmt->execute();     $result = $stmt->get_result();     $film = $result->fetch_assoc();     $stmt->close();     if (!$film) {         die("Film bulunamadı.");     }     $title = $film["title"];     $genre = $film["description"]; } ?> <!DOCTYPE html> <html lang="tr"> <head>     <meta charset="UTF-8">     <title>Filmi Güncelle</title> </head> <body>     <h2>✏️ Filmi Güncelle</h2>     
<p><a href="index.php" class="back-button">Geri dön</a>
</p>
<?php if ($success): ?><p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?><?php if ($error): ?><p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?><form method="post" action="">
    <label for="title">Film Adı:</label>

    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>



    <label for="genre">Tür:</label>

    <input type="text" name="genre" id="genre" value="<?php echo htmlspecialchars($genre); ?>">



    <button type="submit">Güncelle</button>
</form>
 
</body> </html> 