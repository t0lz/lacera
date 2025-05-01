<?php
session_start();
require_once('db_connect.php');

if (!isset($_GET['id_product'])) {
    header("Location: catalog.php");
    exit();
}

$productId = $_GET['id_product'];

$productQuery = pg_query_params($connection, 
    "SELECT p.*, b.name as brand_name, c.name as country_name, cat.name as category_name
     FROM products p
     JOIN brands b ON p.id_brand = b.id_brand
     JOIN countries c ON p.id_country = c.id_country
     JOIN categories cat ON p.id_category = cat.id_category
     WHERE p.id_product = $1", 
    array($productId));

$product = pg_fetch_assoc($productQuery);

if (!$product) {
    header("Location: catalog.php");
    exit();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Нет доступа']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Редактирование товара - LACERA</title>
    <link rel="stylesheet" href="/main/css/sneakers.css" />
    <link rel="stylesheet" href="/main/css/edit_product.css" />
</head>
<body>

<?php require_once('header.php'); ?>

<div class="product-page">
    <h1>Редактирование товара: <?= htmlspecialchars($product['name']) ?></h1>

    <form action="update_product.php" method="POST" id="edit-product-form">
        <input type="hidden" name="id_product" value="<?= $product['id_product'] ?>">

        <label for="name">Название:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <label for="price">Цена:</label>
        <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>

        <label for="description">Описание:</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

        <label for="image">Изображение:</label>
        <input type="file" id="image" name="image" accept="image/*">

        <button type="submit">Обновить товар</button>
    </form>

    <a href="catalog.php" class="back-link">Вернуться в каталог</a>
</div>

<script>
    document.getElementById('edit-product-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Товар успешно обновлен!');
                window.location.href = 'catalog.php';
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при обновлении товара');
        });
    });
</script>

</body>
</html>
