<?php
session_start();

include "src/db.php";
include "src/functions.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = [];
    $nama = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $conPass = $_POST['confirm-password'];
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    if (strlen($nama) > 200) {
        $error[] = "nama tidak boleh lebih dari 200 karakter";
    }
    if (strlen($email) > 200) {
        $error[] = "email tidak boleh lebih dari 200 karakter";
    }
    if ($result->num_rows > 0) {
        $error[] = "email sudah terdaftar";
    }
    if ($pass != $conPass) {
        $error[] = "password tidak sama";
    } else {
        if (strlen($pass) > 200) {
            $error[] = "password tidak boleh lebih dari 200 karakter";
        }
        if (strlen($pass) < 6) {
            $error[] = "password tidak boleh kurang dari 6 karakter";
        }
    }
    if (empty($error)) {
        $_SESSION['login_berhasil'] = true;
        $hashPass = hash_password($pass);
        $sql = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hashPass', 'pemasok')";
        mysqli_query($conn, $sql);
        redirect('login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Sistem Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen font-sans">
    <?php if(!empty($error)) { ?>
        <div class="p-3 w-full max-w-md my-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <strong class="font-bold flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8zM9 4a1 1 0 112 0v5a1 1 0 11-2 0V4zm0 8a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd" />
                </svg>
                Terdapat <?= count($error) ?> error di dalam
            </strong>
            <ul class="mt-2 ml-6 list-disc list-inside text-sm">
                <?php
                foreach($error as $e) {
                    echo "<li>$e</li>";
                }
                ?>
            </ul>
        </div>
    <?php }?>
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-4xl font-bold text-center text-gray-800 mb-8">Buat Akun Baru</h2>
        <form action="" method="POST">
            <div class="mb-5">
                <label for="name" class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="<?= $_POST['name'] ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200" placeholder="Masukkan nama lengkap Anda" required>
            </div>
            <div class="mb-5">
                <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                <input type="email" id="email" name="email" value="<?= $_POST['email'] ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200" placeholder="Masukkan email Anda" required>
            </div>
            <div class="mb-5">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200" placeholder="Buat password" required>
            </div>
            <div class="mb-6">
                <label for="confirm-password" class="block text-gray-700 text-sm font-semibold mb-2">Konfirmasi Password</label>
                <input type="password" id="confirm-password" name="confirm-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200" placeholder="Konfirmasi password Anda" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300 shadow-md">Daftar Akun</button>
        </form>
        <p class="text-center text-gray-600 text-sm mt-6">
            Sudah punya akun?
            <a href="login.php" class="text-blue-600 hover:underline font-semibold">Login sekarang</a>
        </p>
        <p class="text-center text-gray-600 text-sm mt-3">
            <a href="index.php" class="text-blue-600 hover:underline">Kembali ke Beranda</a>
        </p>
    </div>
    <footer class="bg-gray-800 text-white py-4 text-center absolute bottom-0 w-full">
        <div class="container mx-auto px-6">
            <p class="text-sm">&copy; 2025 Sistem Inventory. Hak Cipta Dilindungi.</p>
        </div>
    </footer>
</body>

</html>