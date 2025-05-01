<?php
session_start();
require_once 'db_connect.php';

$error = '';
$success = '';
$email = '';
$remember = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    try {
        if (empty($email) || empty($password)) {
            throw new Exception('Все поля обязательны для заполнения!');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Некорректный email адрес!');
        }

        $query = "
          SELECT u.id_user, u.id_role, r.name AS role_name
          FROM users u
          JOIN roles r ON u.id_role = r.id_role
          WHERE u.email = $1 AND u.password = crypt($2, u.password)
        ";
        $result = pg_query_params($connection, $query, array($email, $password));

        if (!$result) {
            throw new Exception('Ошибка базы данных: ' . pg_last_error($connection));
        }

        $data = pg_fetch_assoc($result);

        if (!$data) {
            throw new Exception('Неверный email или пароль');
        }

        $_SESSION['user_id'] = $data['id_user'];
        $_SESSION['user_role'] = $data['id_role'];
        $_SESSION['role_name'] = $data['role_name'];

        $success = 'Добро пожаловать, ' . htmlspecialchars($email) . '!';

        if ($remember) {
            setcookie('user_email', $email, time() + 60*60*24*30, '/');
        }

        header('Location: /main/php/main.php');
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if (empty($email) && isset($_COOKIE['user_email'])) {
    $email = $_COOKIE['user_email'];
    $remember = true;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Вход</title>
  <link rel="stylesheet" href="/main/css/login.css">
</head>
<body>
    
  <div class="login-container">
    <form class="login-form" method="POST" action="login.php">
      <h2>Вход</h2>
      <a href="/main/php/main.php" class="back-arrow">&#8592;</a>
      
      <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <label for="email">Электронная почта</label>
      <input type="email" id="email" name="email" placeholder="Введите электронную почту" required
             value="<?php echo htmlspecialchars($email); ?>">

      <label for="password">Пароль</label>
      <input type="password" id="password" name="password" placeholder="Введите пароль" required>

      <div class="remember-forgot">
        <label>
          <input type="checkbox" name="remember" <?php echo $remember ? 'checked' : ''; ?>> Запомнить меня
        </label>
      </div>

      <button type="submit" class="login-btn">Вход</button>

      <div class="register-link">
        Нет аккаунта? <a href="/main/php/registration.php">Зарегистрироваться</a>
      </div>
    </form>
  </div>
</body>
</html>
