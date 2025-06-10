<?php
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

$editor_id = $_SESSION['user_id'] ?? null;

if (!$editor_id) {
   header('location:user_login.php');
   exit;
}

include 'config.php';

$select_profile = $conn->prepare("SELECT * FROM `user` WHERE id = ?");
$select_profile->execute([$editor_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
?>

<header class="header">

   <section class="flex">

      <a href="editor_dashboard.php" class="logo"><span>Editor</span>Panel</a>

      <nav class="navbar">
         <a href="editor_dashboard.php">Domů</a>
         <a href="editor_products.php">Pokrmy</a>
         <a href="editor_orders.php">Objednávky</a>
         <a href="editor_profile.php">Profil restaurace</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <p><?= htmlspecialchars($fetch_profile['name'] ?? 'Editor'); ?></p>
         <a href="editor_logout.php" class="delete-btn">Odhlásit se</a>
      </div>

   </section>

</header>

<script>
   document.querySelector('#menu-btn').onclick = () => {
      document.querySelector('.navbar').classList.toggle('active');
   };

   document.querySelector('#user-btn').onclick = () => {
      document.querySelector('.profile').classList.toggle('active');
   };
</script>
