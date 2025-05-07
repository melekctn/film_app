<link rel="stylesheet" href="create.css">
<?php require 'db.php'; include 'auth.php'; $title = ""; $genre = ""; $success = ""; $error = ""; if ($_SERVER["REQUEST_METHOD"] === "POST") {     $title = isset($_POST["title"]) ? htmlspecialchars(trim($_POST["title"])) : "";    $genre = isset($_POST["genre"]) ? htmlspecialchars(trim($_POST["genre"])) : "";     if (empty($title)) {         $error = "Film adı boş bırakılamaz.";     } else {         $stmt = $conn->prepare("INSERT INTO films (title, description) VALUES (?, ?)");         if ($stmt) {             $stmt->bind_param("ss", $title, $genre);             if ($stmt->execute()) {                 $success = "Film başarıyla eklendi.";                 $title = "";                 $genre = "";             } else {                 $error = "Veritabanına eklenirken hata oluştu.";             }             $stmt->close();         } else {             $error = "Veritabanı sorgusu hazırlanamadı.";         }     } } ?> <!DOCTYPE html> <html lang="tr"> <head>    
     <meta charset="UTF-8">     <title>Yeni Film Ekle</title> </head> <body>         
<p><a href="index.php" class="back-button"> Geri Dön</a></p>
<?php if ($success): ?><p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?><?php if ($error): ?><p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?><form method="post" action="">
    <h2>➕ Yeni Film Ekle</h2>
    <label for="title">Film Adı:</label>

    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>



    <label for="genre">Tür:</label>

    <input type="text" name="genre" id="genre" value="<?php echo htmlspecialchars($genre); ?>">



    <button type="submit">Kaydet</button>
</form>
</body> </html>