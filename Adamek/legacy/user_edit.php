<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
   header('location:admin_login.php');
   exit;
}

$user_id = $_GET['edit'] ?? null;
if (!$user_id) {
   header('location:users_accounts.php');
   exit;
}

$stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
   header('location:users_accounts.php');
   exit;
}

if (isset($_POST['update'])) {
   $name = $_POST['name'];
   $email = $_POST['email'];
   $password = !empty($_POST['password']) ? sha1($_POST['password']) : $user['password'];
   $role = $_POST['role'];

   $update = $conn->prepare("UPDATE user SET name = ?, email = ?, password = ?, role = ? WHERE id = ?");
   $update->execute([$name, $email, $password, $role, $user_id]);

   header("location:users_accounts.php");
   exit;
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
   <meta charset="UTF-8">
   <title>Upravit uživatele</title>
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php' ?>

<section class="form-container">
   <form action="" method="post">
      <h3>Upravit uživatele #<?= $user['id']; ?></h3>
      <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required class="box" placeholder="Jméno">
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required class="box" placeholder="Email">
      <input type="password" name="password" class="box" placeholder="Nové heslo (ponech prázdné pro zachování)">
      <select name="role" class="box">
         <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Uživatel</option>
         <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
         <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
      </select>
      <input type="submit" value="Uložit změny" name="update" class="btn">
   </form>
</section>

</body>
</html>
