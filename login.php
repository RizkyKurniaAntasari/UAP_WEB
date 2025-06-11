<?php
// UAP_WEB/login.php

session_start();
require_once 'src/db.php';
require_once 'src/functions.php';

$script_name = dirname($_SERVER['SCRIPT_NAME']);

if (is_user_logged_in()) {
    redirect_to($script_name . '/');
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_user_input($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (verify_user_password($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            redirect_to($script_name . '/');
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Invalid username or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sintory - Login</title>
    <link rel="stylesheet" href="<?php echo $script_name; ?>/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Sintory Login</h2>
        <?php if ($error_message): ?>
            <p class="message error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="<?php echo $script_name; ?>/login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="button button-primary">Login</button>
        </form>
        <p class="login-links">Don't have an account? <a href="<?php echo $script_name; ?>/register.php">Register here</a></p>
    </div>
</body>
</html>