<?php
session_start();
require_once('db_connect.php');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Мужская обувь</title>
    <link rel="stylesheet" href="/main/css/sneakers.css" />
</head>
<body>
<?php
$pageTitle = "Мужская обувь - LACERA";
require_once('header.php');
?>
    <div class="search-bar">
        <form method="GET">
            <div class="search-container">
                <input type="text" name="search" placeholder="Поиск по названию или описанию..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit" class="search-btn">Поиск</button>
            </div>
        </form>
    </div>

    <div class="main-content">
        <aside class="filter-sidebar">
            <h3>Фильтр</h3>
            <form method="GET">
                <div>
                    <label for="brand">Бренд:</label><br>
                    <select id="brand" name="brand">
                        <option value="">Все</option>
                        <?php
                        $brandsQuery = pg_query($connection, 
                            "SELECT DISTINCT b.id_brand, b.name 
                             FROM brands b
                             JOIN products p ON b.id_brand = p.id_brand
                             JOIN categories cat ON p.id_category = cat.id_category
                             WHERE cat.id_category = (SELECT id_category FROM categories WHERE name = 'Мужская')
                             ORDER BY b.name");
                        while ($brand = pg_fetch_assoc($brandsQuery)) {
                            $selected = (isset($_GET['brand']) && $_GET['brand'] == $brand['id_brand']) ? 'selected' : '';
                            echo '<option value="'.$brand['id_brand'].'" '.$selected.'>'.htmlspecialchars($brand['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="country">Страна:</label><br>
                    <select id="country" name="country">
                        <option value="">Все</option>
                        <?php
                        $countriesQuery = pg_query($connection, 
                            "SELECT DISTINCT c.id_country, c.name 
                             FROM countries c
                             JOIN products p ON c.id_country = p.id_country
                             JOIN categories cat ON p.id_category = cat.id_category
                             WHERE cat.id_category = (SELECT id_category FROM categories WHERE name = 'Мужская')
                             ORDER BY c.name");
                        while ($country = pg_fetch_assoc($countriesQuery)) {
                            $selected = (isset($_GET['country']) && $_GET['country'] == $country['id_country']) ? 'selected' : '';
                            echo '<option value="'.$country['id_country'].'" '.$selected.'>'.htmlspecialchars($country['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="min-price">Цена от:</label><br>
                    <input type="number" id="min-price" name="min_price" placeholder="₽" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '' ?>">
                </div>
                <div>
                    <label for="price">Цена до:</label><br>
                    <input type="number" id="price" name="price" placeholder="₽" value="<?= isset($_GET['price']) ? htmlspecialchars($_GET['price']) : '' ?>">
                </div>
                <div style="margin-top: 10px;">
                    <button type="submit">Применить</button>
                </div>
            </form>
        </aside>

        <section class="products-section">
            <div class="products-container">
                <?php
                $sql = "SELECT p.*, b.name as brand_name, c.name as country_name 
                        FROM products p
                        JOIN brands b ON p.id_brand = b.id_brand
                        JOIN countries c ON p.id_country = c.id_country
                        JOIN categories cat ON p.id_category = cat.id_category
                        WHERE cat.id_category = (SELECT id_category FROM categories WHERE name = 'Мужская')";
                
                $params = [];
                $paramCount = 0;

                if (!empty($_GET['search'])) {
                    $paramCount++;
                    $sql .= " AND (p.name ILIKE $".$paramCount." OR p.description ILIKE $".$paramCount.")";
                    $params[] = '%'.$_GET['search'].'%';
                }

                if (!empty($_GET['brand'])) {
                    $paramCount++;
                    $sql .= " AND p.id_brand = $".$paramCount;
                    $params[] = $_GET['brand'];
                }
                
                if (!empty($_GET['country'])) {
                    $paramCount++;
                    $sql .= " AND p.id_country = $".$paramCount;
                    $params[] = $_GET['country'];
                }
                
                if (!empty($_GET['min_price'])) {
                    $paramCount++;
                    $sql .= " AND p.price >= $".$paramCount;
                    $params[] = $_GET['min_price'];
                }

                if (!empty($_GET['price'])) {
                    $paramCount++;
                    $sql .= " AND p.price <= $".$paramCount;
                    $params[] = $_GET['price'];
                }
                
                $sql .= " ORDER BY p.created_at DESC";
                
                $result = pg_query_params($connection, $sql, $params);
                
                if (!$result) {
                    echo '<div class="error">Ошибка при загрузке товаров: ' . pg_last_error($connection) . '</div>';
                } else {
                    while ($product = pg_fetch_assoc($result)) {
                        echo '<div class="product-card">';
                        echo '<img src="/main/picture/'.htmlspecialchars($product['image'] ?? 'sneakers.png').'" alt="'.htmlspecialchars($product['name']).'">';
                        echo '<h3 class="product-title" title="'.htmlspecialchars($product['name']).'">'.htmlspecialchars($product['name']).'</h3>';
                        echo '<div class="price">'.number_format($product['price'], 0, '', ' ').' ₽</div>';
                        echo '<div class="product-card-footer">
                                <a href="product.php?id_product='.$product['id_product'].'" class="product-link">Перейти</a>
                              </div>';
                        echo '</div>';
                    }
                    
                    if (pg_num_rows($result) == 0) {
                        echo '<div class="no-products">Товары не найдены</div>';
                    }
                }
                ?>
            </div>
        </section>
    </div>
</body>
</html>
