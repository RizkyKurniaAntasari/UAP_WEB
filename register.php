<?php
// UAP_WEB/register.php

session_start();
require_once 'src/db.php';
require_once 'src/functions.php';

$script_name = dirname($_SERVER['SCRIPT_NAME']);

if (is_user_logged_in()) {
    redirect_to($script_name . '/');
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_user_input($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } else {
        $hashed_password = hash_user_password($password);

        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error_message = "Username already exists. Please choose a different one.";
        } else {
            $stmt_insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            $stmt_insert->bind_param("ss", $username, $hashed_password);

            if ($stmt_insert->execute()) {
                $success_message = "Registration successful! You can now log in.";
            } else {
                $error_message = "Error registering user: " . $conn->error;
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sintory - Register</title>
    <link rel="stylesheet" href="<?php echo $script_name; ?>/assets/css/style.css">
</head>
<body>
    <div class="register-container">
        <h2>Sintory Register</h2>
        <?php if ($error_message): ?>
            <p class="message error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p class="message success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form action="<?php echo $script_name; ?>/register.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit" class="button button-primary">Register</button>
        </form>
        <p class="register-links">Already have an account? <a href="<?php echo $script_name; ?>/login.php">Login here</a></p>
    </div>
</body>
</html>