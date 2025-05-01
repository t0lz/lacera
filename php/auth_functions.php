<?php
require_once 'db_connect.php';

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: /main/php/login.php');
        exit();
    }
}

function getCurrentUser() {
    if (!isAuthenticated()) return null;
    
    global $connection;
    $query = "SELECT * FROM get_user_data($1)";
    $result = pg_query_params($connection, $query, [$_SESSION['user_id']]);
    
    return pg_fetch_assoc($result);
}

function logout() {
    if (isAuthenticated()) {
        global $connection;
        $query = "SELECT logout_user($1)";
        pg_query_params($connection, $query, [$_SESSION['user_id']]);
    }
    
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

function checkPermission($required_role) {
    if (!isAuthenticated()) return false;
    
    global $connection;
    $query = "SELECT id_role FROM users WHERE id_user = $1";
    $result = pg_query_params($connection, $query, [$_SESSION['user_id']]);
    $user_role = pg_fetch_result($result, 0, 0);
    
    return $user_role == $required_role;
}
?>
