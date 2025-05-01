<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Нет доступа']);
    exit();
}

if (!isset($_POST['id_product'])) {
    echo json_encode(['success' => false, 'message' => 'ID товара не передан']);
    exit();
}

$productId = (int) $_POST['id_product'];

$result = pg_query_params($connection, "SELECT delete_product($1)", [$productId]);

if ($result) {
    $row = pg_fetch_row($result);
    if ($row[0] === 't') { 
        echo json_encode(['success' => true, 'message' => 'Товар успешно удален']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при удалении товара']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}
?>
