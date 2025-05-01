<?php
session_start();
require_once('db_connect.php');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Каталог - LACERA</title>
  <link rel="stylesheet" href="../css/catalog.css" />
</head>
<body>
<?php
$pageTitle = "Каталог - LACERA";
require_once('header.php');

$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] == 2;
?>

<main class="main-section">
  <div class="text-content">
    <h1>Каталог</h1>
  </div>

  <?php if ($is_admin): ?>
    <a href="add_product.php" class="btn-add-product">Добавить новый товар</a>
  <?php endif; ?>

  <div class="catalog">
    <div class="catalog-item">
      <img src="/main/picture/sneakers.png" alt="Мужские кроссовки" />
      <h3>Мужские кроссовки</h3>
      <p>Надежные и удобные кроссовки для активного образа жизни.</p>
      <a href="menssneakers.php" class="more-btn">Посмотреть</a>
    </div>

    <div class="catalog-item">
      <img src="/main/picture/sneakers.png" alt="Женские кроссовки" />
      <h3>Женские кроссовки</h3>
      <p>Стильные и удобные кроссовки для повседневного использования.</p>
      <a href="womenssneakers.php" class="more-btn">Посмотреть</a>
    </div>

    <div class="catalog-item">
      <img src="/main/picture/sneakers.png" alt="Детские кроссовки" />
      <h3>Детские кроссовки</h3>
      <p>Кроссовки для детей с максимальным комфортом и поддержкой.</p>
      <a href="childrenssneakers.php" class="more-btn">Посмотреть</a>
    </div>
  </div>
</main>

</body>
</html>
