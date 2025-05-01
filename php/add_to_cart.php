<?php
session_start();
require_once('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Необходимо авторизоваться';
    header('Location: product.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'Неправильный метод запроса';
    header('Location: product.php');
    exit();
}

$required = ['id_product', 'size'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error_message'] = 'Не все обязательные поля заполнены';
        header('Location: product.php');
        exit();
    }
}

$userId = $_SESSION['user_id'];
$productId = $_POST['id_product'];
$size = $_POST['size'];

$query = "SELECT add_to_cart($1, $2, $3) AS result";
$result = pg_query_params($connection, $query, [$userId, $productId, $size]);

if (!$result) {
    $_SESSION['error_message'] = 'Ошибка базы данных: ' . pg_last_error($connection);
    header('Location: product.php');
    exit();
}

$data = json_decode(pg_fetch_assoc($result)['result'], true);

if ($data['success']) {
    $_SESSION['order_success'] = 'Товар успешно добавлен в корзину!';
    header('Location: product.php');
    exit();
} else {
    $_SESSION['error_message'] = $data['message'];
    header('Location: product.php');
    exit();
}
?>
