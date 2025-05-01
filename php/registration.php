<?php
session_start();
require_once 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first-name']);
    $last_name = trim($_POST['last-name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $error = 'Все поля обязательны для заполнения!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email!';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов!';
    } else {
        $query = "SELECT register_user($1, $2, $3, $4, $5, $6) AS result";
        $result = pg_query_params($connection, $query, [
            $username,
            $first_name,
            $last_name,
            $email,
            $password,
            1
        ]);
        
        if ($result) {
            $data = json_decode(pg_fetch_assoc($result)['result'], true);
            
            if ($data['success']) {
                $_SESSION['user_id'] = $data['user_id'];
                $success = 'Регистрация прошла успешно! Теперь вы можете войти.';
                $username = $first_name = $last_name = $email = $password = '';
            } else {
                $error = $data['message'];
            }
        } else {
            $error = 'Ошибка при регистрации: ' . pg_last_error($connection);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Регистрация</title>
  <link rel="stylesheet" href="/main/css/registration.css">
</head>
<body> 
  <div class="registration-container">
    <form class="registration-form" method="POST" action="registration.php">
      <h2>Регистрация</h2>
      <a href="/main/php/main.php" class="back-arrow">&#8592;</a>

      <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <label for="username">Имя пользователя</label>
      <input type="text" id="username" name="username" placeholder="Введите имя пользователя" required 
             value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">

      <label for="first-name">Ваше имя</label>
      <input type="text" id="first-name" name="first-name" placeholder="Введите ваше имя" required 
             value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : ''; ?>">

      <label for="last-name">Ваша фамилия</label>
      <input type="text" id="last-name" name="last-name" placeholder="Введите вашу фамилию" required 
             value="<?php echo isset($last_name) ? htmlspecialchars($last_name) : ''; ?>">

      <label for="email">Электронная почта</label>
      <input type="email" id="email" name="email" placeholder="Введите электронную почту" required 
             value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">

      <label for="password">Пароль</label>
      <input type="password" id="password" name="password" placeholder="Введите пароль (минимум 6 символов)" required>

      <button type="submit" class="registration-btn">Зарегистрироваться</button>

      <div class="account-link">
        Есть аккаунт? <a href="login.php" class="login-link-text">Войти</a>
      </div>
    </form>
  </div>
</body>
</html>
