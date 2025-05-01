<?php
session_start();
require_once('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    pg_query($connection, "BEGIN");

    $order_query = "SELECT create_order_with_number($1) AS order_id";
    $order_result = pg_query_params($connection, $order_query, [$_SESSION['user_id']]);

    if (!$order_result) {
        throw new Exception("Ошибка при создании заказа: " . pg_last_error($connection));
    }

    $order_data = pg_fetch_assoc($order_result);
    $order_id = $order_data['order_id'];

    if (!$order_id) {
        throw new Exception("Не удалось создать заказ.");
    }

    pg_query($connection, "COMMIT");

    $_SESSION['order_success'] = "Заказ #$order_number успешно оформлен!";
    header('Location: profile.php');
    exit();

} catch (Exception $e) {
    pg_query($connection, "ROLLBACK");
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: cart.php');
    exit();
}

