<?php

require_once __DIR__ . '/../backend/Middleware/AuthMiddleware.php';
AuthMiddleware::check();

require_once __DIR__ . '/../backend/config/Database.php';
require_once __DIR__ . '/../backend/controller/ProductController.php';

$controller = new ProductController();
$message = $controller->handleRequest();

?>

<?php include 'admin_header.php'; ?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Správa produktů</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .status {
            font-size: 1.2rem;
            margin-top: 0.5rem;
            font-weight: bold;
        }
        .status.approved {
            color: green;
        }
        .status.pending {
            color: orange;
        }
    </style>
</head>
<body>

<?php
if (!empty($message)) {
    foreach ($message as $msg) {
        echo '<div class="message">
                <span>' . htmlspecialchars($msg) . '</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
              </div>';
    }
}
?>

<section class="add-products">
   <h1 class="heading">Přidat produkt</h1>

   <form action="" method="post" enctype="multipart/form-data">
   <input type="text" class="box" required maxlength="100" placeholder="zadej název produktu" name="name">
   <input type="number" min="0" class="box" required max="9999999999" placeholder="zadej cenu produktu" onkeypress="if(this.value.length == 10) return false;" name="price">
   <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>

   <select name="restaurant_id" class="box" required>
      <option value="" disabled <?= !isset($_POST['restaurant_id']) ? 'selected' : '' ?>>-- vyber restauraci --</option>
      <?php
      $pdo = Database::connect();
      $stmt = $pdo->query("SELECT id, name FROM restaurants");
      while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
         $selected = (isset($_POST['restaurant_id']) && $_POST['restaurant_id'] == $r['id']) ? 'selected' : '';
         echo '<option value="' . (int)$r['id'] . '" ' . $selected . '>' . htmlspecialchars($r['name']) . '</option>';
      }
      ?>
   </select>

   <input type="submit" value="přidat produkt" class="btn" name="add_product">
</form>
</section>

<section class="show-products">
   <h1 class="heading">Přidané produkty</h1>

   <div class="box-container">
      <?php
      $pdo = Database::connect();
      $stmt = $pdo->prepare("SELECT * FROM products");
      $stmt->execute();
      $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($products as $product):
      ?>
         <div class="box">
            <img src="../uploaded_img/<?= htmlspecialchars($product['image']) ?>" alt="">
            <div class="name"><?= htmlspecialchars($product['name']) ?></div>
            <div class="price"><?= htmlspecialchars($product['price']) ?> Kč</div>
            
            <?php if (isset($product['approved']) && $product['approved'] == 1): ?>
                <div class="status approved">✅ Schváleno</div>
            <?php else: ?>
                <div class="status pending">⏳ Čeká na schválení</div>
            <?php endif; ?>

            <a href="?delete=<?= $product['id'] ?>" class="delete-btn" onclick="return confirm('smazat tento produkt?');">smazat</a>
         </div>
      <?php endforeach; ?>
   </div>
</section>

<script>
setTimeout(() => {
    document.querySelectorAll('.message').forEach(el => el.remove());
}, 4000);
</script>

<script src="../js/admin_script.js"></script>
</body>
</html>
