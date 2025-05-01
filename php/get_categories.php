<?php
require_once('db_connect.php');

$query = "SELECT id_category, name FROM categories";
$result = pg_query($connection, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Ошибка запроса к базе данных']);
    exit();
}

$categories = [];
while ($row = pg_fetch_assoc($result)) {
    $categories[] = $row;
}

echo json_encode($categories);
?>
