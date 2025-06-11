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
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-app-color': '#1976d2',
                        'primary-app-color-dark': '#1565c0',
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
        <h2 class="text-primary-app-color text-3xl font-bold mb-8">Sintory Register</h2>
        <?php if ($error_message): ?>
            <p class="mb-4 p-3 rounded-md text-sm text-red-700 bg-red-100 border border-red-200"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p class="mb-4 p-3 rounded-md text-sm text-green-700 bg-green-100 border border-green-200"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form action="<?php echo $script_name; ?>/register.php" method="POST" class="space-y-4">
            <div class="text-left">
                <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Username</label>
                <input type="text" name="username" id="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" class="shadow-sm appearance-none border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-app-color focus:border-primary-app-color-dark transition duration-200" required>
            </div>
            <div class="text-left">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input type="password" name="password" id="password" class="shadow-sm appearance-none border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-app-color focus:border-primary-app-color-dark transition duration-200" required>
            </div>
            <div class="text-left">
                <label for="confirm_password" class="block text-gray-700 text-sm font-semibold mb-2">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="shadow-sm appearance-none border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-app-color focus:border-primary-app-color-dark transition duration-200" required>
            </div>
            <button type="submit" class="w-full bg-primary-app-color hover:bg-primary-app-color-dark text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-200 mt-6">
                Register
            </button>
        </form>
        <p class="mt-4 text-sm text-gray-600">Already have an account? <a href="<?php echo $script_name; ?>/login.php" class="text-primary-app-color hover:underline">Login here</a></p>
    </div>
</body>
</html>