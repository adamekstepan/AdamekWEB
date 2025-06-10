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

$restaurant_stmt = $conn->prepare("SELECT * FROM restaurants WHERE editor_id = ?");
$restaurant_stmt->execute([$editor_id]);
$restaurant = $restaurant_stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) {
   echo "<p style='text-align:center; padding:2rem;'>Nemáš přiřazenou restauraci.</p>";
   exit;
}

$restaurant_id = $restaurant['id'];

if (isset($_POST['add_product'])) {
   $name = $_POST['name'];
   $price = $_POST['price'];
   $image = $_FILES['image']['name'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_img/' . $image;

   move_uploaded_file($image_tmp_name, $image_folder);

   $insert = $conn->prepare("INSERT INTO products (name, price, image, restaurant_id, approved) VALUES (?, ?, ?, ?, 0)");
   $insert->execute([$name, $price, $image, $restaurant_id]);
   $message = "Pokrmu byl přidán a čeká na schválení adminem.";
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $conn->prepare("DELETE FROM products WHERE id = ? AND restaurant_id = ?")->execute([$delete_id, $restaurant_id]);
   header('location:editor_products.php');
   exit;
}

$products = $conn->prepare("SELECT * FROM products WHERE restaurant_id = ?");
$products->execute([$restaurant_id]);

?>

<!DOCTYPE html>
<html lang="cs">
<head>
   <meta charset="UTF-8">
   <title>Správa jídel - <?= htmlspecialchars($restaurant['name']) ?></title>
   <link rel="stylesheet" href="../css/editor_style.css">
   <link rel="stylesheet" href="../css/editor_products.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>

<?php include 'editor_header.php'; ?>

<section class="editor-products">

   <h1 class="heading">Správa jídel – <?= htmlspecialchars($restaurant['name']) ?></h1>

   <?php if (!empty($message)): ?>
      <div class="message">
         <span><?= $message; ?></span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
   <?php endif; ?>

   <form action="" method="post" enctype="multipart/form-data" class="add-form">
      <h3>Přidat nový pokrm</h3>
      <input type="text" name="name" required class="box" placeholder="název jídla" maxlength="100">
      <input type="number" name="price" required class="box" placeholder="cena v CZK" min="0">
      <input type="file" name="image" required accept="image/*" class="box">
      <input type="submit" value="Přidat pokrm" name="add_product" class="btn">
   </form>

   <div class="box-container">
      <?php while ($row = $products->fetch(PDO::FETCH_ASSOC)): ?>
         <div class="box">
            <img src="../uploaded_img/<?= htmlspecialchars($row['image']); ?>" alt="náhled">
            <p class="name"><?= htmlspecialchars($row['name']); ?></p>
            <p class="price">Cena: <span>CZK<?= htmlspecialchars($row['price']); ?></span></p>
            <p class="status"><?= $row['approved'] ? '✅ Schváleno' : '⏳ Čeká na schválení'; ?></p>
            <a href="editor_products.php?delete=<?= $row['id']; ?>" class="btn delete" onclick="return confirm('Smazat tento pokrm?')">Smazat</a>
         </div>
      <?php endwhile; ?>
   </div>

</section>
<script src="../js/admin_script.js"></script>
</body>
</html>
