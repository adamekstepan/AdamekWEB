<?php
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
   header('location:admin_login.php');
   exit;
}

include 'config.php';

// Získání dat o adminovi
$select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
?>

<header class="header">

   <section class="flex">

      <a href="admin_page.php" class="logo">Admin<span>Panel</span></a>

      <nav class="navbar">
         <a href="admin_page.php">Domů</a>
         <a href="admin_products.php">Produkty</a>
         <a href="admin_orders.php">Objednávky</a>
         <a href="admin_accounts.php">Admini</a>
         <a href="users_accounts.php">Uživatelé</a>
         <a href="admin_add_restaurant.php">Restaurace</a>
         <a href="admin_approve_products.php">Schvalování jídel</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <p><?= htmlspecialchars($fetch_profile['name'] ?? 'Admin'); ?></p>
         <a href="admin_profile_update.php" class="btn">Upravit profil</a>
         <a href="logout.php" class="delete-btn">Logout</a>
         <div class="flex-btn">
            <a href="admin_login.php" class="option-btn">Login</a>
            <a href="admin_register.php" class="option-btn">Register</a>
         </div>
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
