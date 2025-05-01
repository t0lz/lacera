<?php
session_start();
require_once('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $query = "SELECT username, firstname, lastname, email FROM users WHERE id_user = $1";
    $result = pg_query_params($connection, $query, [$_SESSION['user_id']]);
    
    if (!$result) {
        throw new Exception("Ошибка при получении данных пользователя: " . pg_last_error($connection));
    }
    
    $user = pg_fetch_assoc($result);
    if (!$user) {
        throw new Exception("Пользователь не найден");
    }

    $orders_query = "SELECT order_number, order_date, total, 
                            (SELECT COUNT(*) FROM order_items WHERE id_order = o.id_order) AS item_count
                     FROM orders o
                     WHERE id_user = $1
                     ORDER BY order_date DESC";
    $orders_result = pg_query_params($connection, $orders_query, [$_SESSION['user_id']]);
    
    if (!$orders_result) {
        throw new Exception("Ошибка при получении заказов: " . pg_last_error($connection));
    }
    
    $orders = pg_fetch_all($orders_result) ?: [];

} catch (Exception $e) {
    error_log("Ошибка в profile.php: " . $e->getMessage());
    $_SESSION['error_message'] = "Произошла ошибка при загрузке профиля";
    $user = ['username' => '', 'firstname' => '', 'lastname' => '', 'email' => ''];
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : "Профиль пользователя - LACERA" ?></title>
    <link rel="stylesheet" href="/main/css/profile.css">
</head>
<body>
    <?php require_once('header.php'); ?>
    
    <div class="profile-container">
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-header">
            <h2>Профиль пользователя</h2>
        </div>
        
        <div class="profile-info">
            <div>
                <span class="profile-label">Логин:</span>
                <span><?= htmlspecialchars($user['username']) ?></span>
            </div>
            <div>
                <span class="profile-label">Имя:</span>
                <span><?= htmlspecialchars($user['firstname']) ?></span>
            </div>
            <div>
                <span class="profile-label">Фамилия:</span>
                <span><?= htmlspecialchars($user['lastname']) ?></span>
            </div>
            <div>
                <span class="profile-label">Email:</span>
                <span><?= htmlspecialchars($user['email']) ?></span>
            </div>
        </div>
        
        <?php if (isset($_SESSION['order_success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['order_success']) ?>
                <?php unset($_SESSION['order_success']); ?>
            </div>
        <?php endif; ?>
        
        <div class="orders-section">
            <h3>Мои заказы</h3>
            
            <?php if (empty($orders)): ?>
                <p>У вас пока нет заказов.</p>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <span class="order-id">Заказ № <?= htmlspecialchars($order['order_number']) ?></span>
                            </div>
                            <div class="order-body">
                                <p><strong>Дата и время:</strong> <?= date('d.m.Y H:i', strtotime($order['order_date'])) ?></p>
                                <p><strong>Товары:</strong> <?= (int)$order['item_count'] ?> шт.</p>
                                <p><strong>Сумма:</strong> <?= number_format($order['total'], 0, '', ' ') ?> ₽</p>
                                <a href="order_details.php?order_number=<?= htmlspecialchars($order['order_number']) ?>" class="details-link">Подробнее</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
