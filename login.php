<?php
// Start the session at the very beginning of the script
session_start();
include_once 'src/db.php';
include_once 'src/functions.php';
$errorMessage = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Get and sanitize user input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Password will be hashed, so no sanitization here

    // Basic validation
    if (empty($email) || empty($password)) {
        $errorMessage = 'Email and password are required.';
    } else {
        // 3. Prepare a SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, nama, email, password, role FROM users WHERE email = ?");
        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("s", $email); // 's' indicates a string parameter
        $stmt->execute();
        $result = $stmt->get_result();

        // 4. Check if a user with the given email exists   
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // 5. Verify the password
            // Use password_verify for securely checking hashed passwords
            if (password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role']; // Store the user's role

                // Redirect to a dashboard or home page after successful login
                // You can customize this redirection based on the user's role
                if ($_SESSION['role'] == 'pemasok'){
                    redirect_views_pemasok('/dashboard.php');
                }else{
                    redirect_views_admin('/dashboard.php');
                }
                exit();
            } else {
                // Incorrect password
                $errorMessage = 'Invalid email or password.';
            }
        } else {
            // User not found
            $errorMessage = 'Invalid email or password.';
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
}

// If there's an error, the script will continue to the HTML part and display the error message.
// The HTML code from your provided snippet should be in the same file or included.
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .error-message {
            color: #dc2626; /* red-700 */
            background-color: #fef2f2; /* red-100 */
            border: 1px solid #ef4444; /* red-400 */
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen font-sans">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-4xl font-bold text-center text-gray-800 mb-8">Masuk ke Akun Anda</h2>
        <form id="loginForm" action="login.php" method="POST">
            <?php if (!empty($errorMessage)): ?>
                <div id="errorMessage" class="error-message">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>

            <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="Masukkan email Anda" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="Masukkan password Anda" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300 shadow-md">Login</button>
        </form>
        <p class="text-center text-gray-600 text-sm mt-6">
            Belum punya akun?
            <a href="register.php" class="text-blue-600 hover:underline font-semibold">Daftar sekarang</a>
        </p>
        <p class="text-center text-gray-600 text-sm mt-3">
            <a href="index.php" class="text-blue-600 hover:underline">Kembali ke Beranda</a>
        </p>
    </div>
</body>
</html>