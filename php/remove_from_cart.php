<?php
session_start();
require_once('db_connect.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Необходимо авторизоваться']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$query = "SELECT remove_from_cart($1, $2) AS result";
$result = pg_query_params($connection, $query, [
    $input['id'],
    $_SESSION['user_id']
]);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . pg_last_error($connection)]);
    exit();
}

$data = json_decode(pg_fetch_assoc($result)['result'], true);

if ($data['success']) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $data['message']]);
}
?>
