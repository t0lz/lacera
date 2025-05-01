<?php
session_start();
require_once('db_connect.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Нет доступа']);
    exit();
}

$id_product = $_POST['id_product'];
$name = trim($_POST['name']);
$price = (float) $_POST['price'];
$description = trim($_POST['description']);
$query = "SELECT image FROM products WHERE id_product = $1";
$params = array($id_product);
$result = pg_query_params($connection, $query, $params);
$product = pg_fetch_assoc($result);

$imagePath = $product['image']; 

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $file = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Недопустимый тип файла!']);
        exit();
    }

    $fileName = uniqid('product_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/main/picture/';
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $imagePath = $fileName; 
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при загрузке файла!']);
        exit();
    }
}

$query = "UPDATE products SET name = $1, price = $2, description = $3, image = $4 WHERE id_product = $5";
$params = array($name, $price, $description, $imagePath, $id_product);
$result = pg_query_params($connection, $query, $params);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Товар успешно обновлен!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении товара']);
}

?>
