<?php
session_start();
require_once('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['order_number'])) {
    header('Location: profile.php');
    exit();
}

$order_number = $_GET['order_number'];

try {
    $order_check_query = "SELECT id_order FROM orders WHERE order_number = $1 AND id_user = $2";
    $order_check_result = pg_query_params($connection, $order_check_query, [$order_number, $_SESSION['user_id']]);
    
    if (pg_num_rows($order_check_result) == 0) {
        throw new Exception("Заказ не найден или вам не принадлежит");
    }
    
    $order_query = "SELECT * FROM orders WHERE order_number = $1";
    $order_result = pg_query_params($connection, $order_query, [$order_number]);
    $order = pg_fetch_assoc($order_result);
    
    $items_query = "SELECT oi.*, p.name as product_name, b.name as brand_name
                   FROM order_items oi
                   JOIN products p ON oi.id_product = p.id_product
                   JOIN brands b ON p.id_brand = b.id_brand
                   WHERE oi.id_order = $1";
    $items_result = pg_query_params($connection, $items_query, [$order['id_order']]);
    $items = pg_fetch_all($items_result) ?: [];
    
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: profile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Детали заказа #<?= htmlspecialchars($order['order_number']) ?></title>
    <link rel="stylesheet" href="/main/css/order_details.css">
</head>
<body>
    <?php $pageTitle = "Детали заказа - LACERA"; require_once('header.php'); ?>
    
    <div class="profile-container">
        <div class="order-details">
            <h2 class="order-header">Детали заказа #<?= htmlspecialchars($order['order_number']) ?></h2>
            <div class="order-summary">
                <p class="order-date"><strong>Дата:</strong> <?= date('d.m.Y H:i', strtotime($order['order_date'])) ?></p>
                <p class="order-total"><strong>Итого:</strong> <?= number_format($order['total'], 0, '', ' ') ?> ₽</p>
            </div>

            <h3 class="order-items-header">Товары в заказе</h3>
            <div class="order-items">
                <?php foreach ($items as $item): ?>
                    <div class="order-item">
                        <div class="item-info">
                            <h4><?= htmlspecialchars($item['product_name']) ?></h4>
                            <p><strong>Бренд:</strong> <?= htmlspecialchars($item['brand_name']) ?></p>
                            <p><strong>Размер:</strong> <?= htmlspecialchars($item['size']) ?></p>
                            <p><strong>Количество:</strong> <?= $item['quantity'] ?></p>
                            <p><strong>Цена за единицу:</strong> <?= number_format($item['price_at_order'], 0, '', ' ') ?> ₽</p>
                        </div>
                        <div class="item-subtotal">
                            <strong>Итого:</strong> <?= number_format($item['price_at_order'] * $item['quantity'], 0, '', ' ') ?> ₽
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <a href="profile.php" class="back-link">← Вернуться к списку заказов</a>
        </div>
    </div>
</body>
</html>
