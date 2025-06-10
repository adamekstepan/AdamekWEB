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

// Schválení pokrmu
if (isset($_GET['approve'])) {
   $id = $_GET['approve'];
   $stmt = $conn->prepare("UPDATE products SET approved = 1 WHERE id = ?");
   $stmt->execute([$id]);
   header('location:admin_approve_products.php');
   exit;
}

// Smazání pokrmu
if (isset($_GET['delete'])) {
   $id = $_GET['delete'];
   $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
   $stmt->execute([$id]);
   header('location:admin_approve_products.php');
   exit;
}

// Výběr neschválených pokrmů
$stmt = $conn->prepare("
   SELECT p.*, r.name AS restaurant_name, u.name AS editor_name 
   FROM products p
   LEFT JOIN restaurants r ON p.id = r.id
   LEFT JOIN user u ON r.editor_id = u.id
   WHERE p.approved = 0
   ORDER BY p.id DESC
");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="cs">
<head>
   <meta charset="UTF-8">
   <title>Schválení pokrmů</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="accounts">
   <h1 class="heading">Čekající pokrmy ke schválení</h1>

   <div class="box-container">

   <?php if (count($products) > 0): ?>
      <?php foreach ($products as $product): ?>
         <div class="box">
            <img src="../uploaded_img/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" style="width:100%; max-width:200px;">
            <p><strong>Název:</strong> <?= htmlspecialchars($product['name']); ?></p>
            <p><strong>Cena:</strong> CZK<?= htmlspecialchars($product['price']); ?>,-</p>
            <p><strong>Restaurace:</strong> <?= htmlspecialchars($product['restaurant_name']); ?></p>
            <p><strong>Editor:</strong> <?= htmlspecialchars($product['editor_name']); ?></p>
            <a href="admin_approve_products.php?approve=<?= $product['id']; ?>" class="btn">✅ Schválit</a>
            <a href="admin_approve_products.php?delete=<?= $product['id']; ?>" class="delete-btn" onclick="return confirm('Opravdu smazat tento pokrm?')">❌ Smazat</a>
         </div>
      <?php endforeach; ?>
   <?php else: ?>
      <p class="empty">Žádné nové pokrmy k potvrzení.</p>
   <?php endif; ?>

   </div>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>
