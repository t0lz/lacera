<?php
session_start();
require_once('db_connect.php');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/main.css" />
</head>
<body>
<?php
$pageTitle = "Главная - LACERA";
require_once('header.php');
?>
  <main class="main-section">
    <div class="text-content">
      <h1>Вдохновляйтесь на новые шаги</h1>
      <p>Шагайте с комфортом и стилем</p>
      <a href="/main/php/catalog.php"><button class="more-btn">Перейти в каталог</button></a>
    </div>
  </main>
</body>
</html>
