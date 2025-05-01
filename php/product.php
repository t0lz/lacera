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

$sizesQuery = pg_query_params($connection,
    "SELECT size 
     FROM product_sizes 
     WHERE id_product = $1 
     ORDER BY size::integer",
    array($productId));

$is_admin = false;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $roleQuery = pg_query_params($connection, "SELECT id_role FROM users WHERE id_user = $1", array($userId));
    $userData = pg_fetch_assoc($roleQuery);
    if ($userData && $userData['id_role'] == 2) { 
        $is_admin = true;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($product['name']) ?> - LACERA</title>
    <link rel="stylesheet" href="/main/css/sneakers.css" />
    <link rel="stylesheet" href="/main/css/product.css" />
</head>
<body>
<?php
require_once('header.php');
?>
    <div class="product-page">
        <div class="product-image">
            <img src="/main/picture/<?= htmlspecialchars($product['image'] ?? 'sneakers.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <a href="javascript:history.back()" class="back-arrow">&#8592;</a>
        </div>
        
        <div class="product-info">
            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
            
            <div class="product-price"><?= number_format($product['price'], 0, '', ' ') ?> ₽</div>
            
            <div class="product-details">
                <div class="detail-row">
                    <span class="detail-label">Бренд:</span>
                    <span><?= htmlspecialchars($product['brand_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Страна:</span>
                    <span><?= htmlspecialchars($product['country_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Категория:</span>
                    <span><?= htmlspecialchars($product['category_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Описание:</span>
                    <span><?= htmlspecialchars($product['description']) ?></span>
                </div>
            </div>        
            <form id="add-to-cart-form" action="add_to_cart.php" method="POST">
                <input type="hidden" name="id_product" value="<?= $product['id_product'] ?>">
                
                <div class="size-selector">
                    <label for="size">Размер:</label>
                    <select id="size" name="size" required>
                        <option value="">Выберите размер</option>
                        <?php 
                        pg_result_seek($sizesQuery, 0);
                        while ($sizeRow = pg_fetch_assoc($sizesQuery)): 
                            $size = $sizeRow['size'];
                        ?>
                            <option value="<?= $size ?>">
                                <?= htmlspecialchars($size) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <?php if ($is_admin): ?>
                    <div class="admin-buttons">
                        <a href="edit_product.php?id_product=<?= $product['id_product'] ?>" class="admin-link">Редактировать</a>
                        <button type="button" class="admin-link delete-link" id="delete-product" onclick="deleteProduct(<?= $product['id_product'] ?>)">Удалить</button>
                    </div>
                <?php endif; ?>
                <button type="submit" class="add-to-cart">Добавить в корзину</button>
            </form>
        </div>
    </div>

    <script>
        function deleteProduct(productId) {
            if (confirm('Вы уверены, что хотите удалить товар?')) {
                fetch('delete_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        'id_product': productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Товар успешно удален');
                        window.location.href = 'catalog.php';
                    } else {
                        alert('Ошибка: ' + (data.message || 'Не удалось удалить товар'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка при удалении товара');
                });
            }
        }
    </script>
</body>
</html>
