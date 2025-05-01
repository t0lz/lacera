<?php
require_once('db_connect.php');

$query = "SELECT id_brand, name FROM brands";
$result = pg_query($connection, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Ошибка запроса к базе данных']);
    exit();
}

$brands = [];
while ($row = pg_fetch_assoc($result)) {
    $brands[] = $row;
}

echo json_encode($brands);
?>
