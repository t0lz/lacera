<?php
session_start();
require_once('db_connect.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    error_log("Ошибка: Нет доступа к добавлению товара. Пользователь не имеет нужных прав.\n", 3, $_SERVER['DOCUMENT_ROOT'] . '/error_log.txt');
    echo json_encode(['success' => false, 'message' => 'Нет доступа']);
    exit();
}

if (
    empty($_POST['name']) || 
    empty($_POST['price']) || 
    empty($_POST['description']) || 
    empty($_POST['id_brand']) ||
    empty($_POST['id_country']) ||
    empty($_POST['id_category'])
) {
    error_log("Ошибка: Пропущены обязательные поля. Name: {$_POST['name']}, Price: {$_POST['price']}, Description: {$_POST['description']}, Brand: {$_POST['id_brand']}, Country: {$_POST['id_country']}, Category: {$_POST['id_category']}\n", 3, $_SERVER['DOCUMENT_ROOT'] . '/error_log.txt');
    echo json_encode(['success' => false, 'message' => 'Заполните все поля корректно!']);
    exit();
}

$name = trim($_POST['name']);
$price = (float) $_POST['price']; 
$description = trim($_POST['description']);
$id_brand = (int) $_POST['id_brand'];
$id_country = (int) $_POST['id_country'];
$id_category = (int) $_POST['id_category'];

$imagePath = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $file = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        error_log("Ошибка: Недопустимый тип файла. Тип файла: $fileType\n", 3, $_SERVER['DOCUMENT_ROOT'] . '/error_log.txt');
        echo json_encode(['success' => false, 'message' => 'Недопустимый тип файла!']);
        exit();
    }

    $fileName = uniqid('product_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/main/picture/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $imagePath = $fileName;
    } else {
        error_log("Ошибка: Не удалось загрузить изображение. Файл: $fileName\n", 3, $_SERVER['DOCUMENT_ROOT'] . '/error_log.txt');
        echo json_encode(['success' => false, 'message' => 'Ошибка при загрузке изображения!']);
        exit();
    }
} else {
    error_log("Ошибка: Изображение не загружено.\n", 3, $_SERVER['DOCUMENT_ROOT'] . '/error_log.txt');
    echo json_encode(['success' => false, 'message' => 'Изображение не загружено!']);
    exit();
}
$query = "INSERT INTO products (name, price, description, image, id_brand, id_country, id_category, created_at) 
          VALUES ($1, $2, $3, $4, $5, $6, $7, NOW())";
$params = array($name, $price, $description, $imagePath, $id_brand, $id_country, $id_category);

$result = pg_query_params($connection, $query, $params);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Товар успешно добавлен!']);
} else {
    $error = pg_last_error($connection);
    error_log("Ошибка при добавлении товара в базу данных. Запрос: $query, Параметры: " . implode(", ", $params) . ", Ошибка: $error\n", 3, $_SERVER['DOCUMENT_ROOT'] . '/error_log.txt');
    echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении товара.']);
}
?>
