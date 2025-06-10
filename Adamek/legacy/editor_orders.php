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

// Načtení restaurace
$stmt = $conn->prepare("SELECT * FROM restaurants WHERE editor_id = ?");
$stmt->execute([$editor_id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) {
   echo "<p style='text-align:center; padding:2rem;'>Nemáš přiřazenou restauraci.</p>";
   exit;
}

$restaurant_id = $restaurant['id'];

if (isset($_POST['update_payment'])) {
   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $payment_status = filter_var($payment_status, FILTER_SANITIZE_STRING);
   $update = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ? AND restaurant_id = ?");
   $update->execute([$payment_status, $order_id, $restaurant_id]);
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete = $conn->prepare("DELETE FROM `orders` WHERE id = ? AND restaurant_id = ?");
   $delete->execute([$delete_id, $restaurant_id]);
   header('location:editor_orders.php');
   exit;
}

// Výběr objednávek pro restauraci
$stmt = $conn->prepare("SELECT * FROM `orders` WHERE restaurant_id = ? ORDER BY placed_on DESC");
$stmt->execute([$restaurant_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="cs">
<head>
   <meta charset="UTF-8">
   <title>Objednávky editora</title>
   <link rel="stylesheet" href="../css/admin_style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>

<?php include 'editor_header.php'; ?>

<section class="orders">

   <h1 class="heading">Objednávky – <?= htmlspecialchars($restaurant['name']); ?></h1>

   <div class="box-container">

   <?php if (count($orders) > 0): ?>
      <?php foreach ($orders as $order): ?>
         <div class="box">
            <p>Vytvořeno: <span><?= $order['placed_on']; ?></span></p>
            <p>Jméno: <span><?= $order['name']; ?></span></p>
            <p>Telefon: <span><?= $order['number']; ?></span></p>
            <p>Adresa: <span><?= $order['address']; ?></span></p>
            <p>Objednávka: <span><?= $order['total_products']; ?></span></p>
            <p>Cena: <span>CZK<?= $order['total_price']; ?>,-</span></p>
            <p>Platba: <span><?= $order['method']; ?></span></p>
            <form action="" method="post">
               <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
               <select name="payment_status" class="select">
                  <option value="zpracováváno" <?= $order['payment_status'] == 'zpracováváno' ? 'selected' : '' ?>>zpracováváno</option>
                  <option value="hotovo" <?= $order['payment_status'] == 'hotovo' ? 'selected' : '' ?>>hotovo</option>
               </select>
               <div class="flex-btn">
                  <input type="submit" value="upravit" name="update_payment" class="option-btn">
                  <a href="editor_orders.php?delete=<?= $order['id']; ?>" class="delete-btn" onclick="return confirm('Opravdu smazat objednávku?');">vymazat</a>
               </div>
            </form>
         </div>
      <?php endforeach; ?>
   <?php else: ?>
      <p class="empty">Žádné objednávky k zobrazení.</p>
   <?php endif; ?>

   </div>

</section>
<script src="../js/admin_script.js"></script>
</body>
</html>
