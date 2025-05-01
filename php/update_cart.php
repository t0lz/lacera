<?php
session_start();
require_once('db_connect.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Необходимо авторизоваться']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$query = "UPDATE cart_items SET quantity = $1 WHERE id_cart_item = $2 AND id_user = $3";
$result = pg_query_params($connection, $query, [
    $input['quantity'],
    $input['id'],
    $_SESSION['user_id']
]);

echo json_encode(['success' => $result !== false]);
?>
