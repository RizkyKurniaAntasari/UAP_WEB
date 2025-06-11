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
            set_flash_message("Login successful!", "success");
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
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Sekarang ini adalah warna utama aplikasi Anda
                        'primary-app-color': '#1976d2',
                        'primary-app-color-dark': '#1565c0',
                        // Warna lain jika masih ada, tapi ini akan jadi default
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $script_name; ?>/assets/css/style.css">
</head>
<body class="flex flex-col min-h-screen items-center justify-center bg-gray-100 text-gray-800 font-poppins">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md border border-gray-200 text-center">
        <h2 class="text-primary-app-color text-3xl font-bold mb-8">Sintory Login</h2>
        <?php if ($error_message): ?>
            <p class="mb-4 p-3 rounded-md text-sm text-red-700 bg-red-100 border border-red-200"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="<?php echo $script_name; ?>/login.php" method="POST" class="space-y-4" autocomplete="off">
            <div class="text-left">
                <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Username</label>
                <input type="text" name="username" id="username" class="shadow-sm appearance-none border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-app-color focus:border-primary-app-color-dark transition duration-200" required>
            </div>
            <div class="text-left">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input type="password" name="password" id="password" class="shadow-sm appearance-none border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-app-color focus:border-primary-app-color-dark transition duration-200" required>
            </div>
            <button type="submit" class="w-full bg-primary-app-color hover:bg-primary-app-color-dark text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-200 mt-6">
                Login
            </button>
        </form>
        <p class="mt-4 text-sm text-gray-600">Don't have an account? <a href="<?php echo $script_name; ?>/register.php" class="text-primary-app-color hover:underline">Register here</a></p>
    </div>
</body>
</html>