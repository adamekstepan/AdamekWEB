<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `user` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:users_accounts.php');
   exit;
}

if (isset($_GET['set_role']) && isset($_GET['user_id'])) {
   $new_role = $_GET['set_role'];
   $user_id = $_GET['user_id'];

   if (in_array($new_role, ['user', 'editor', 'admin'])) {
      $update = $conn->prepare("UPDATE `user` SET role = ? WHERE id = ?");
      $update->execute([$new_role, $user_id]);
   }

   header('location:users_accounts.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
   <meta charset="UTF-8">
   <title>Uživatelské účty</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <link rel="stylesheet" href="../css/admin_accounts.css">
</head>
<body>

<?php include 'admin_header.php' ?>

<section class="accounts">

   <h1 class="heading">Uživatelské účty</h1>

   <div style="margin-bottom: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
      <a href="users_accounts.php" class="btn" style="width:auto;">Všichni</a>
      <a href="users_accounts.php?filter=user" class="btn" style="width:auto;">Uživatelé</a>
      <a href="users_accounts.php?filter=editor" class="btn" style="width:auto;">Editoři</a>
   </div>

   <div class="box-container">

   <?php
      $filter = $_GET['filter'] ?? null;

      if ($filter && in_array($filter, ['user', 'editor', 'admin'])) {
         $select_accounts = $conn->prepare("SELECT * FROM `user` WHERE role = ?");
         $select_accounts->execute([$filter]);
      } else {
         $select_accounts = $conn->prepare("SELECT * FROM `user`");
         $select_accounts->execute();
      }

      if ($select_accounts->rowCount() > 0) {
         while ($fetch_accounts = $select_accounts->fetch(PDO::FETCH_ASSOC)) {
   ?>
   <div class="box">
      <p>ID: <span><?= $fetch_accounts['id']; ?></span></p>
      <p>Jméno: <span><?= $fetch_accounts['name']; ?></span></p>
      <p>Email: <span><?= $fetch_accounts['email']; ?></span></p>
      <p>Role: <span><?= $fetch_accounts['role']; ?></span></p>
      
      <!-- Tlačítka na změnu role -->
      <div class="role-buttons">
         <a href="users_accounts.php?set_role=user&user_id=<?= $fetch_accounts['id']; ?>" class="btn" style="width:auto;">user</a>
         <a href="users_accounts.php?set_role=editor&user_id=<?= $fetch_accounts['id']; ?>" class="btn" style="width:auto;">editor</a>
         <a href="users_accounts.php?set_role=admin&user_id=<?= $fetch_accounts['id']; ?>" class="btn" style="width:auto;">admin</a>
         <a href="edit_user.php?id=<?= $fetch_accounts['id']; ?>" class="edit-btn">Upravit</a>
      </div>

      <a href="users_accounts.php?delete=<?= $fetch_accounts['id']; ?>" onclick="return confirm('Smazat účet?')" class="delete-btn">Smazat</a>
   </div>
   <?php
         }
      } else {
         echo '<p class="empty">Zatím žádní uživatelé!</p>';
      }
   ?>

   </div>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>
