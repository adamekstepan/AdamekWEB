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

// Počet pokrmů
$stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE restaurant_id = ?");
$stmt->execute([$restaurant_id]);
$total_products = $stmt->fetchColumn();

// Počet objednávek
$stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE restaurant_id = ?");
$stmt->execute([$restaurant_id]);
$total_orders = $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html lang="cs">
<head>
   <meta charset="UTF-8">
   <title>Editor dashboard</title>
   <link rel="stylesheet" href="../css/editor_style.css">
   <link rel="stylesheet" href="../css/editor_dashboard.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>

<?php include 'editor_header.php'; ?>

<section class="editor-dashboard">

   <h1 class="heading">Dashboard restaurace</h1>

   <div class="box-container">

      <div class="box">
         <p><strong>Název:</strong> <span><?= htmlspecialchars($restaurant['name']); ?></span></p>
         <p><strong>Popis:</strong> <span><?= htmlspecialchars($restaurant['description']); ?></span></p>
         <p><strong>Počet jídel:</strong> <span><?= $total_products; ?></span></p>
         <p><strong>Počet objednávek:</strong> <span><?= $total_orders; ?></span></p>
      </div>

      <div class="box">
         <a href="editor_products.php" class="btn">Spravovat pokrmy</a>
         <a href="editor_orders.php" class="btn">Zobrazit objednávky</a>
         <a href="editor_profile.php" class="btn">Upravit profil restaurace</a>
      </div>

   </div>

</section>
<script src="../js/admin_script.js"></script>
</body>
</html>
