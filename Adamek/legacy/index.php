<?php

include 'config.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['register'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass'] );
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `user` WHERE name = ? AND email = ?");
   $select_user->execute([$name, $email]);

   if($select_user->rowCount() > 0){
      $message[] = 'jméno nebo email už existuje!';
   }else{
      if($pass != $cpass){
         $message[] = 'hesla se neshodují!';
      }else{
         $insert_user = $conn->prepare("INSERT INTO `user`(name, email, password) VALUES(?,?,?)");
         $insert_user->execute([$name, $email, $cpass]);
         $message[] = 'úspešně registrován, přihlašte se!';
      }
   }

}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = 'počet aktualizován!';
}

if(isset($_GET['delete_cart_item'])){
   $delete_cart_id = $_GET['delete_cart_item'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$delete_cart_id]);
   header('location:index.php');
}

if(isset($_GET['logout'])){
   session_unset();
   session_destroy();
   header('location:index.php');
}

if(isset($_POST['add_to_cart'])){

   if($user_id == ''){
      $message[] = 'prosím nejdříve se přihlašte!';
   }else{

      $pid = $_POST['pid'];
      $name = $_POST['name'];
      $price = $_POST['price'];
      $image = $_POST['image'];
      $qty = $_POST['qty'];
      $qty = filter_var($qty, FILTER_SANITIZE_STRING);

      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND name = ?");
      $select_cart->execute([$user_id, $name]);

      if($select_cart->rowCount() > 0){
         $message[] = 'nachází se v košíku';
      }else{
         $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
         $insert_cart->execute([$user_id, $pid, $name, $price, $qty, $image]);
         $message[] = 'přidáno do košíku!';
      }

   }

}

if(isset($_POST['order'])){

   if($user_id == ''){
      $message[] = 'prosím nejdříve se přihlašte!';
   }else{
      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $number = $_POST['number'];
      $number = filter_var($number, FILTER_SANITIZE_STRING);
      $address = ''.$_POST['flat'].', '.$_POST['street'].' - '.$_POST['pin_code'];
      $address = filter_var($address, FILTER_SANITIZE_STRING);
      $method = $_POST['method'];
      $method = filter_var($method, FILTER_SANITIZE_STRING);
      $total_price = $_POST['total_price'];
      $total_products = $_POST['total_products'];

      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);

      if($select_cart->rowCount() > 0){
         $restaurant_query = $conn->prepare("SELECT p.restaurant_id 
   FROM cart c 
   JOIN products p ON c.pid = p.id 
   WHERE c.user_id = ? 
   LIMIT 1");
$restaurant_query->execute([$user_id]);
$restaurant_id = $restaurant_query->fetchColumn();

$insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, method, address, total_products, total_price, restaurant_id) VALUES(?,?,?,?,?,?,?,?)");
$insert_order->execute([$user_id, $name, $number, $method, $address, $total_products, $total_price, $restaurant_id]);
         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_id]);
         $message[] = 'objednávka přijata!';
      }else{
         $message[] = 'košík je prázdný!';
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Complete Responsive Food Shop Website Design</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<?php
   if(isset($message)){
      foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
?>

<!-- header section starts  -->

<header class="header">

   <section class="flex">

      <a href="#home" class="logo"><span>Dobro</span>žrout.</a>

      <nav class="navbar">
         <a href="#home">Domů</a>
         <a href="#about">O nás</a>
         <a href="#menu">Nabídka</a>
         <a href="#order">Objednat</a>
         <a href="#faq">Otázky</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="order-btn" class="fas fa-box"></div>
         <?php
            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_items = $count_cart_items->rowCount();
         ?>
         <div id="cart-btn" class="fas fa-shopping-cart"><span>(<?= $total_cart_items; ?>)</span></div>
      </div>

   </section>

</header>

<!-- header section ends -->

<div class="user-account">

   <section>

      <div id="close-account"><span>zavřít</span></div>

      <div class="user">
         <?php
            $select_user = $conn->prepare("SELECT * FROM `user` WHERE id = ?");
            $select_user->execute([$user_id]);
            if($select_user->rowCount() > 0){
               while($fetch_user = $select_user->fetch(PDO::FETCH_ASSOC)){
                  echo '<p>Vítejte! <span>'.$fetch_user['name'].'</span></p>';
                  echo '<a href="index.php?logout" class="btn">Odhlásit</a>';
               }
            }else{
               echo '<p><span>momentálně nejste přihlášeni!</span></p>';
            }
         ?>
      </div>

      <div class="display-orders">
         <?php
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if($select_cart->rowCount() > 0){
               while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                  echo '<p>'.$fetch_cart['name'].' <span>('.$fetch_cart['price'].' x '.$fetch_cart['quantity'].')</span></p>';
               }
            }else{
               echo '<p><span>Váš košík je prázdný!</span></p>';
            }
         ?>
      </div>

      <div class="flex">

         <form action="user_login.php" method="post">
            <h3>Přihlaš se</h3>
            <input type="email" name="email" required class="box" placeholder="zadejte email" maxlength="50">
            <input type="password" name="pass" required class="box" placeholder="zadejte heslo" maxlength="20">
            <input type="submit" value="přihlásit" name="login" class="btn">
         </form>

         <form action="" method="post">
            <h3>Registruj se</h3>
            <input type="text" name="name" oninput="this.value = this.value.replace(/\s/g, '')" required class="box" placeholder="zadejte jméno" maxlength="20">
            <input type="email" name="email" required class="box" placeholder="zadejte email" maxlength="50">
            <input type="password" name="pass" required class="box" placeholder="zadejte heslo" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="cpass" required class="box" placeholder="znovu helso" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="registrovat" name="register" class="btn">
         </form>

      </div>

   </section>

</div>

<div class="my-orders">

   <section>

      <div id="close-orders"><span>zavřít</span></div>

      <h3 class="title"> moje objednávky </h3>

      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
         $select_orders->execute([$user_id]);
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){   
      ?>
      <div class="box">
         <p><b>vytvořeno: </b><span><?= $fetch_orders['placed_on']; ?></span> </p>
         <p><b>jméno: </b><span><?= $fetch_orders['name']; ?></span> </p>
         <p><b>tel. číslo: </b><span><?= $fetch_orders['number']; ?></span> </p>
         <p><b>adresa: </b><span><?= $fetch_orders['address']; ?></span> </p>
         <p><b>zbůsob platby: </b><span><?= $fetch_orders['method']; ?></span> </p>
         <p><b>objednávka: </b><span><?= $fetch_orders['total_products']; ?></span> </p>
         <p><b>celková cena: </b><span>CZK<?= $fetch_orders['total_price']; ?>,-</span> </p>
         <p><b>status objednávky: </b><span style="color:<?php if($fetch_orders['payment_status'] == 'zpracováváno'){ echo 'red'; }else{ echo 'green'; }; ?>"><?= $fetch_orders['payment_status']; ?></span> </p>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">zatím nic neobjednáno!</p>';
      }
      ?>

   </section>

</div>

<div class="shopping-cart">

   <section>

      <div id="close-cart"><span>zavřít</span></div>

      <?php
         $grand_total = 0;
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
              $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
              $grand_total += $sub_total; 
      ?>
      <div class="box">
         <a href="index.php?delete_cart_item=<?= $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('chcete smazat položku?');"></a>
         <img src="../uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
         <div class="content">
          <p> <?= $fetch_cart['name']; ?> <span>(<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?>)</span></p>
          <form action="" method="post">
             <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
             <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>" onkeypress="if(this.value.length == 2) return false;">
               <button type="submit" class="fas fa-edit" name="update_qty"></button>
          </form>
         </div>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty"><span>Váš košík je prázdný!</span></p>';
      }
      ?>

      <div class="cart-total"> celkem : <span>CZK<?= $grand_total; ?>,-</span></div>

      <a href="#order" class="btn">objednat</a>

   </section>

</div>

<div class="home-bg">

   <section class="home" id="home">

      <div class="slide-container">

         <div class="slide active">
            <div class="image">
               <img src="../images/rizoto.png" alt="1">
            </div>
            <div class="content">
               <h2>Doporučujeme</h2>
               <h3>Zeleninové rizoto</h3>
               <div class="fas fa-angle-left" onclick="prev()"></div>
               <div class="fas fa-angle-right" onclick="next()"></div>
            </div>
         </div>

         <div class="slide">
            <div class="image">
               <img src="../images/pizza.png" alt="2">
            </div>
            <div class="content">
               <h2>Doporučujeme</h2>
               <h3>Rajčatová pizza</h3>
               <div class="fas fa-angle-left" onclick="prev()"></div>
               <div class="fas fa-angle-right" onclick="next()"></div>
            </div>
         </div>

         <div class="slide">
            <div class="image">
               <img src="../images/steak.png" alt="3">
            </div>
            <div class="content">
               <h2>Doporučujeme</h2>
               <h3>Steak se zeleninou</h3>
               <div class="fas fa-angle-left" onclick="prev()"></div>
               <div class="fas fa-angle-right" onclick="next()"></div>
            </div>
         </div>

      </div>

   </section>

</div>

<!-- about section starts  -->

<section class="about" id="about">

   <h1 class="heading">O nás</h1>

   <div class="box-container">

      <div class="box">
      <img src="../images/onas1.jpg" alt="onas1">
         <h3>pečlivě připravováno</h3>
         <p>Kvalitní příprava jídla začíná výběrem čerstvých a kvalitních surovin.
            Používání správných kuchařských technik.
            Kvalitní příprava jídla zahrnuje pečlivou pozornost k detailům, jako je časování jednotlivých kroků a estetická úprava talíře, což celkově zlepšuje zážitek z jídla.
         </p>
         <a href="#menu" class="btn">Nabídka</a>
      </div>

      <div class="box">
      <img src="../images/onas3.jpg" alt="onas3">
         <h3>sdílej s přáteli</h3>
         <p>Společné jídlo podporuje pocit sounáležitosti a sdílené zážitky, které prohlubují přátelství. 
            Během společného jídla lze ochutnat různé pokrmy a tím poznávat nové kultury a tradice. 
            Sdílení jídla s přáteli přináší radost a uspokojení.</p>
         <a href="#menu" class="btn">Nabídka</a>
      </div>

      <div class="box">
         <img src="../images/onas2.png" alt="onas2">
         <h3>rychlé doručení</h3>
         <p>Rychlé doručení jídla šetří čas.
            Efektivní doručovací služby zajišťují, že jídlo dorazí čerstvé a v optimální teplotě.
            Rychlé doručení jídla poskytuje pohodlí.
         </p>
         <a href="#menu" class="btn">Nabídka</a>
      </div>

   </div>

</section>

<!-- about section ends -->

<!-- menu section starts  -->

<section id="menu" class="menu">

   <h1 class="heading">Nabídka</h1>

   <div class="box-container">

      <?php
         $select_products = $conn->prepare("
   SELECT p.*, r.name AS restaurant_name
   FROM products p
   JOIN restaurants r ON p.restaurant_id = r.id
   WHERE p.approved = 1
");
         $select_products->execute();
         if($select_products->rowCount() > 0){
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){    
      ?>
      <div class="box">
   <div class="price">CZK<?= $fetch_products['price'] ?>,-</div>
   <img src="../uploaded_img/<?= $fetch_products['image'] ?>" alt="">
   <div class="name"><?= $fetch_products['name'] ?></div>
   <p class="restaurant">Restaurace: <?= htmlspecialchars($fetch_products['restaurant_name']); ?></p>
   <form action="" method="post">
      <input type="hidden" name="pid" value="<?= $fetch_products['id'] ?>">
      <input type="hidden" name="name" value="<?= $fetch_products['name'] ?>">
      <input type="hidden" name="price" value="<?= $fetch_products['price'] ?>">
      <input type="hidden" name="image" value="<?= $fetch_products['image'] ?>">
      <input type="number" name="qty" class="qty" min="1" max="99" value="1">
      <input type="submit" class="btn" name="add_to_cart" value="Přidat do košíku">
   </form>
</div>
      <?php
         }
      }else{
         echo '<p class="empty">Zatím žádné položky!</p>';
      }
      ?>

   </div>

</section>

<!-- menu section ends -->

<!-- order section starts  -->

<section class="order" id="order">

   <h1 class="heading">Objednat</h1>

   <form action="" method="post">

   <div class="display-orders">

   <?php
         $grand_total = 0;
         $cart_item[] = '';
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
              $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
              $grand_total += $sub_total; 
              $cart_item[] = $fetch_cart['name'].' ( '.$fetch_cart['price'].' x '.$fetch_cart['quantity'].' ) - ';
              $total_products = implode($cart_item);
              echo '<p>'.$fetch_cart['name'].' <span>('.$fetch_cart['price'].' x '.$fetch_cart['quantity'].')</span></p>';
            }
         }else{
            echo '<p class="empty"><span>Váš košík je prázdný!</span></p>';
         }
      ?>

   </div>

      <div class="grand-total"> Celkem: <span>CZK<?= $grand_total; ?>,-</span></div>

      <input type="hidden" name="total_products" value="<?= $total_products; ?>">
      <input type="hidden" name="total_price" value="<?= $grand_total; ?>">

      <div class="flex">
         <div class="inputBox">
            <span>jméno: </span>
            <input type="text" name="name" class="box" required placeholder="zadejte jméno" maxlength="20">
         </div>
         <div class="inputBox">
            <span>tel. číslo: </span>
            <input type="number" name="number" class="box" required placeholder="zadejte telefonní číslo" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;">
         </div>
         <div class="inputBox">
            <span>Způsob platby: </span>
            <select name="method" class="box">
               <option value="dobírka">dobírka</option>
               <option value="kreditní karta">kreditní carta</option>
               <option value="platební karta">platební karta</option>
               <option value="paypal">paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Město: </span>
            <input type="text" name="flat" class="box" required placeholder="název města/obce" maxlength="50">
         </div>
         <div class="inputBox">
            <span>Ulice: </span>
            <input type="text" name="street" class="box" required placeholder="název ulice" maxlength="50">
         </div>
         <div class="inputBox">
            <span>Číslo popisné: </span>
            <input type="number" name="pin_code" class="box" required placeholder="číslo popísné" min="0" max="999999" onkeypress="if(this.value.length == 6) return false;">
         </div>
      </div>

      <input type="submit" value="Objednat" class="btn" name="order">

   </form>

</section>

<!-- order section ends -->

<!-- faq section starts  -->

<section class="faq" id="faq">

   <h1 class="heading">Časté otázky</h1>

   <div class="accordion-container">

   <div class="accordion active">
         <div class="accordion-heading">
            <span>Jak dlouho trvá dovoz jídla?</span>
            <i class="fas fa-angle-down"></i>
         </div>
         <p class="accrodion-content">
            Doba doručení jídla se může lišit v závislosti na několika faktorech, jako je vzdálenost mezi restaurací a místem doručení, aktuální provoz na silnicích, čas doručení (např. večer bývá větší poptávka), a také efektivita samotné doručovací služby. Obvykle se většina objednávek snaží doručit do 30 až 60 minut od potvrzení objednávky, ale může se lišit podle konkrétních okolností.
         </p>
      </div>

      <div class="accordion">
         <div class="accordion-heading">
            <span>Jak aplikace funguje?</span>
            <i class="fas fa-angle-down"></i>
         </div>
         <p class="accrodion-content">
            Uživatel si prostřednictvím aplikace vybere pokrm, který si přeje objednat.
            Poté, co uživatel vybere jídlo, provede objednávku přes aplikaci. Může si vybrat způsob platby a zadat adresu doručení.
            Restaurace nebo dodavatel dostane objednávku prostřednictvím aplikace a potvrdí ji.
            Restaurace připraví jídlo a doručovací služba se postará o jeho doručení na zadanou adresu.
            Přijetí objednávky uživatelem.
         </p>
      </div>

      <div class="accordion">
         <div class="accordion-heading">
            <span>Jaké podniky jsou v nabídce?</span>
            <i class="fas fa-angle-down"></i>
         </div>
         <p class="accrodion-content">
            Nabízíme širokou škálu pokrmu, z těch nejkvalitnějších surovin. V nabídce jsou velmi cenněné a kvalitní podniky s výbornou přípravou pokrmů.
         </p>
      </div>

   </div>

</section>

<!-- faq section ends -->

<!-- footer section starts  -->

<section class="footer">

<div class="box-container">

<div class="box">
   <i class="fas fa-phone"></i>
   <h3>Telefonní číslo</h3>
   <p>123 456 789</p>
   <p>987 654 321</p>
</div>

<div class="box">
   <i class="fas fa-clock"></i>
   <h3>čas dovážky</h3>
   <p>6:00 - 1:00</p>
</div>

<div class="box">
   <i class="fas fa-map-marker-alt"></i>
   <h3>sídlo</h3>
   <p>Rooseveltova 47, 537 01 Chrudim 1</p>
</div>

<div class="box">
   <i class="fas fa-envelope"></i>
   <h3>email</h3>
   <p>dobro.zrout@seznam.cz</p>
   <p>dobrozrout@gmail.com</p>
</div>

</div>

<div class="credit">
2025<span> Štěpán Adámek</span> 
</div>

</section>

<!-- footer section ends -->



















<!-- custom js file link  -->
<script src="../js/script.js"></script>

</body>
</html>