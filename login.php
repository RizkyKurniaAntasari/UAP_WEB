<?php
session_start();
include_once 'src/db.php';
include_once 'src/functions.php';
$errorMessage = '';
$submittedEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi & sanitasi input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $submittedEmail = $email;

    // Jika admin hardcode
    if ($email === 'admin@example.com' && $password === 'admin123') {
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = 0;
        $_SESSION['nama'] = 'Admin';
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'admin';
        redirect_views_admin('/dashboard.php');
        exit();
    }

    // Cek input kosong
    if (empty($email) || empty($password)) {
        $errorMessage = 'Email dan password wajib diisi.';
    } else {
        // Siapkan statement
        $stmt = $conn->prepare("SELECT id, nama, email, password, role FROM users WHERE email = ?");
        if (!$stmt) {
            die("Query gagal: " . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if ($_SESSION['role'] === 'pemasok') {
                    redirect_views_pemasok('/dashboard.php');
                } else {
                    redirect_views_admin('/dashboard.php');
                }
                exit();
            } else {
                $errorMessage = 'Email atau password salah.';
            }
        } else {
            $errorMessage = 'Email atau password salah.';
        }

        $stmt->close();
        $conn->close();
    }
}
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
            color: #dc2626;
            background-color: #fef2f2;
            border: 1px solid #ef4444;
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
                <div class="error-message">
                    <?= htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>

            <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    autocomplete="email"
                    value="<?= htmlspecialchars($submittedEmail); ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200" 
                    placeholder="Masukkan email Anda" 
                    required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    autocomplete="current-password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200" 
                    placeholder="Masukkan password Anda" 
                    required>
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
