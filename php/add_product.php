<?php
session_start();
require_once('db_connect.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    echo "Нет доступа";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Добавление нового товара - LACERA</title>
    <link rel="stylesheet" href="/main/css/edit_product.css" />
</head>
<body>

<?php require_once('header.php'); ?>

<div class="product-page">
    <h1>Добавление нового товара</h1>

    <form id="add-product-form" enctype="multipart/form-data">
        <label for="name">Название:</label>
        <input type="text" id="name" name="name" required>

        <label for="price">Цена:</label>
        <input type="text" id="price" name="price" required>

        <label for="description">Описание:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="id_brand">Бренд:</label>
        <select id="id_brand" name="id_brand" required>
            <option value="">Выберите бренд</option>
        </select>

        <label for="id_category">Категория:</label>
        <select id="id_category" name="id_category" required>
            <option value="">Выберите категорию</option>
        </select>

        <label for="id_country">Страна:</label>
        <select id="id_country" name="id_country" required>
            <option value="">Выберите страну</option>
        </select>

        <label for="image">Изображение товара:</label>
        <input type="file" id="image" name="image" accept="image/*" required>

        <button type="submit">Добавить товар</button>
    </form>
</div>

<script>
fetch('get_brands.php')
    .then(response => response.json())
    .then(data => {
        const brandSelect = document.getElementById('id_brand');
        data.forEach(brand => {
            const option = document.createElement('option');
            option.value = brand.id_brand;
            option.textContent = brand.name;
            brandSelect.appendChild(option);
        });
    });

fetch('get_categories.php')
    .then(response => response.json())
    .then(data => {
        const categorySelect = document.getElementById('id_category');
        data.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id_category;
            option.textContent = category.name;
            categorySelect.appendChild(option);
        });
    });

fetch('get_countries.php')
    .then(response => response.json())
    .then(data => {
        const countrySelect = document.getElementById('id_country');
        data.forEach(country => {
            const option = document.createElement('option');
            option.value = country.id_country;
            option.textContent = country.name;
            countrySelect.appendChild(option);
        });
    });

document.getElementById('add-product-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch('save_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Товар успешно добавлен!');
            window.location.href = 'catalog.php';
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert('Произошла ошибка при добавлении товара');
    });
});
</script>
</body>
</html>
