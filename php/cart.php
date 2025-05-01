<?php
session_start();
require_once('db_connect.php');

$pageTitle = "Корзина - LACERA";
require_once('header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$query = "SELECT * FROM get_cart_contents($1)";
$result = pg_query_params($connection, $query, [$_SESSION['user_id']]);
$cartItems = pg_fetch_all($result);

$total = 0;
if ($cartItems) {
    foreach ($cartItems as $item) {
        $total += $item['subtotal'];
    }
}
?>
<main class="cart-section">
    <link rel="stylesheet" href="../css/cart.css" />
    <h1>Ваша корзина</h1>

    <div class="cart-items">
        <?php if (empty($cartItems)): ?>
            <p class="empty-cart">Ваша корзина пуста</p>
        <?php else: ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <div class="item-info">
                        <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                        <p>Размер: <?= htmlspecialchars($item['size']) ?></p>
                        <p>Цена: <?= number_format($item['price'], 0, '', ' ') ?> ₽</p>
                        <p>Количество: <?= $item['quantity'] ?></p>
                    </div>
                    <div class="item-price"><?= number_format($item['subtotal'], 0, '', ' ') ?> ₽</div>
                    <button class="remove-btn" data-id="<?= $item['cart_id'] ?>">Удалить</button> 
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (!empty($cartItems)): ?>
<div class="cart-summary">
    <p>Общая сумма: <span class="total-price"><?= number_format($total, 0, '', ' ') ?> ₽</span></p>
    <form method="POST" action="checkout.php">
        <button type="submit" class="checkout-btn">Оформить заказ</button>
    </form>
</div>
<?php endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const cartId = this.getAttribute('data-id');
            
            if (confirm('Вы уверены, что хотите удалить товар из корзины?')) {
                fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: cartId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Ошибка при удалении товара.');
                    }
                });
            }
        });
    });
});
</script>
