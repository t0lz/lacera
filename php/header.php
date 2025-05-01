<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? $pageTitle : 'LACERA' ?></title>
  <link rel="stylesheet" href="/main/css/main.css" />
  <link rel="stylesheet" href="/main/css/sneakers.css" />
  <link rel="stylesheet" href="/main/css/header.css" />
</head>
<body>
  <header class="header">
    <a href="/main/php/main.php" class="logo">LACERA</a>
    <nav class="nav">
      <a href="/main/php/main.php">Главная</a>
      <a href="/main/php/catalog.php">Каталог</a>
      <a href="https://vk.com/rokkunroru">Контакты</a>
      <?php if(isset($_SESSION['user_id'])): ?>
        <button class="cart-btn" onclick="location.href='cart.php'">Корзина</button>
        <button class="profile-btn" onclick="location.href='profile.php'">Профиль</button>
        <button class="logout-btn" onclick="location.href='logout.php'">Выход</button>
      <?php else: ?>
        <button class="login-btn" onclick="location.href='login.php'">Вход</button>
      <?php endif; ?>
    </nav>
  </header>
