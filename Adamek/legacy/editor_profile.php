<?php

include 'config.php';


if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

if (!isset($_SESSION['user_id'])) {
   header('location:user_login.php');
   exit;
}

$editor_id = $_SESSION['user_id'];

// Získání restaurace editora
$stmt = $conn->prepare("SELECT * FROM restaurants WHERE editor_id = ?");
$stmt->execute([$editor_id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) {
   echo "<p style='text-align:center; padding:2rem;'>Nemáš přiřazenou restauraci.</p>";
   exit;
}

$restaurant_id = $restaurant['id'];
$message = '';

if (isset($_POST['update'])) {
   $name = $_POST['name'];
   $description = $_POST['description'];

   // Kontrola, zda byl nahrán nový obrázek
   if (!empty($_FILES['image']['name'])) {
      $image = $_FILES['image']['name'];
      $image_tmp = $_FILES['image']['tmp_name'];
      $image_path = '../uploaded_img/' . $image;
      move_uploaded_file($image_tmp, $image_path);

      $stmt = $conn->prepare("UPDATE restaurants SET name = ?, description = ?, image = ? WHERE id = ?");
      $stmt->execute([$name, $description, $image, $restaurant_id]);
   } else {
      $stmt = $conn->prepare("UPDATE restaurants SET name = ?, description = ? WHERE id = ?");
      $stmt->execute([$name, $description, $restaurant_id]);
   }

   $message = 'Profil restaurace byl úspěšně upraven.';
   // Znovu načteme upravené údaje
   $stmt = $conn->prepare("SELECT * FROM restaurants WHERE editor_id = ?");
   $stmt->execute([$editor_id]);
   $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
   <meta charset="UTF-8">
   <title>Úprava profilu restaurace</title>
   <link rel="stylesheet" href="../css/editor_style.css">
   <link rel="stylesheet" href="../css/editor_profile.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>

<?php include 'editor_header.php'; ?>

<section class="editor-profile">

   <h1 class="title">Úprava profilu restaurace</h1>

   <?php if ($message): ?>
      <p class="message"><?= $message; ?></p>
   <?php endif; ?>

   <form action="" method="post" enctype="multipart/form-data">
      <div class="inputBox">
         <span>Název restaurace</span>
         <input type="text" name="name" required class="box" maxlength="100" value="<?= htmlspecialchars($restaurant['name']); ?>">
      </div>
      <div class="inputBox">
         <span>Popis</span>
         <textarea name="description" required class="box" maxlength="1000" rows="4"><?= htmlspecialchars($restaurant['description']); ?></textarea>
      </div>
      <div class="inputBox">
         <span>Obrázek restaurace</span>
         <input type="file" name="image" accept="image/*" class="box">
         <?php if (!empty($restaurant['image'])): ?>
            <img src="../uploaded_img/<?= $restaurant['image']; ?>" alt="obrázek restaurace">
         <?php endif; ?>
      </div>
      <input type="submit" value="Uložit změny" name="update" class="btn">
   </form>

</section>
<script src="../js/admin_script.js"></script>
</body>
</html>

