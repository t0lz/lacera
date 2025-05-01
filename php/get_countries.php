<?php
require_once('db_connect.php');

$query = "SELECT id_country, name FROM countries";
$result = pg_query($connection, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Ошибка запроса к базе данных']);
    exit();
}

$countries = [];
while ($row = pg_fetch_assoc($result)) {
    $countries[] = $row;
}

echo json_encode($countries);
?>
