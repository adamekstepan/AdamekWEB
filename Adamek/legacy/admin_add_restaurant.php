<?php

include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
   header('location:admin_login.php');
   exit;
}

$message = '';

if (isset($_POST['add_restaurant'])) {
   $name = $_POST['name'];
   $description = $_POST['description'];
   $editor_id = $_POST['editor_id'];

   $image = $_FILES['image']['name'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_img/' . $image;

   if (move_uploaded_file($image_tmp_name, $image_folder)) {
      $insert_restaurant = $conn->prepare("INSERT INTO restaurants (name, description, image, editor_id) VALUES (?, ?, ?, ?)");
      $insert_restaurant->execute([$name, $description, $image, $editor_id]);
      $message = 'Restaurace byla úspěšně přidána.';
   } else {
      $message = 'Nepodařilo se nahrát obrázek.';
   }
}

$editors = $conn->query("SELECT * FROM user WHERE role = 'editor'");
$restaurants = $conn->query("
   SELECT r.*, u.name AS editor_name 
   FROM restaurants r 
   LEFT JOIN user u ON r.editor_id = u.id 
   ORDER BY r.id DESC
");

?>

<!DOCTYPE html>
<html lang="cs">
<head>
   <meta charset="UTF-8">
   <title>Přidat restauraci</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <link rel="stylesheet" href="../css/admin_add_restaurant.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<?php if ($message): ?>
   <div class="message">
      <span><?= $message; ?></span>
      <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
   </div>
<?php endif; ?>

<section class="add-products">
   <h1 class="title">Přidat novou restauraci</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <div class="inputBox">
         <span>Název restaurace</span>
         <input type="text" name="name" required placeholder="Zadejte název" class="box" maxlength="100">
      </div>
      <div class="inputBox">
         <span>Popis</span>
         <textarea name="description" required placeholder="Popis restaurace" class="box" maxlength="1000" rows="4"></textarea>
      </div>
      <div class="inputBox">
         <span>Obrázek restaurace</span>
         <input type="file" name="image" accept="image/*" required class="box">
      </div>
      <div class="inputBox">
         <span>Editor (uživatel)</span>
         <select name="editor_id" class="box" required>
            <option value="">-- Vyberte editora --</option>
            <?php while ($editor = $editors->fetch(PDO::FETCH_ASSOC)) : ?>
               <option value="<?= $editor['id']; ?>"><?= $editor['name']; ?> (<?= $editor['email']; ?>)</option>
            <?php endwhile; ?>
         </select>
      </div>
      <input type="submit" value="Přidat restauraci" name="add_restaurant" class="btn">
   </form>
</section>

<section class="restaurant-overview">
   <h2 class="title">Přehled restaurací</h2>

   <div class="box-container">
      <?php while ($row = $restaurants->fetch(PDO::FETCH_ASSOC)): ?>
         <div class="box">
            <img src="../uploaded_img/<?= htmlspecialchars($row['image']); ?>" alt="<?= htmlspecialchars($row['name']); ?>">
            <div class="content">
               <h3><?= htmlspecialchars($row['name']); ?></h3>
               <p><?= htmlspecialchars($row['description']); ?></p>
               <p><strong>Editor:</strong> <?= htmlspecialchars($row['editor_name']); ?></p>
            </div>
         </div>
      <?php endwhile; ?>
   </div>
</section>

<script src="../js/admin_script.js"></script>

</body>
</html>
