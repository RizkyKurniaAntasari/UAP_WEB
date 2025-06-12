<?php

$jumlahProduk = 50;
$pesananBaru = 5;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemasok - Sistem Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans flex flex-col min-h-screen"> <nav class="bg-green-700 p-4 shadow-md text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="dashboard.php" class="text-2xl font-bold">Pemasok Dashboard</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:text-green-200">Beranda</a>
                <a href="my_products.php" class="hover:text-green-200">Produk Saya</a>
                <a href="orders.php" class="hover:text-green-200">Pesanan</a>
                <a href="../../logout.php" class="bg-red-600 px-3 py-1 rounded-md hover:bg-red-700 transition duration-300" onclick="logoutClientSide(event)">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-8 flex-grow"> <h1 class="text-4xl font-bold text-gray-800 mb-6">Selamat Datang, Pemasok!</h1>
        <p class="text-gray-700 mb-8">Di sini Anda dapat melihat informasi terkait produk yang Anda sediakan dan status pesanan.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Produk Anda Terdaftar</h3>
                <p class="text-4xl font-bold text-green-600"><?= $jumlahProduk ?></p>
                <p class="text-gray-500">jenis produk</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Pesanan Baru</h3>
                <p class="text-4xl font-bold text-blue-600"><?= $pesananBaru ?></p>
                <p class="text-gray-500">menunggu konfirmasi</p>
            </div>
        </div>

        <div class="mt-10">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Aksi Cepat</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="my_products.php" class="bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition duration-300 text-center">Lihat Produk Saya</a>
                <a href="orders.php" class="bg-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300 text-center">Lihat Pesanan</a>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 text-center mt-8"> <div class="container mx-auto px-6">
            <p class="text-sm">&copy; 2025 Sistem Inventory. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script>
        function logoutClientSide(event) {
            event.preventDefault();
            localStorage.removeItem('userRole');
            localStorage.removeItem('userEmail');
            window.location.href = '../../logout.php'; // Path ke logout.php di root
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('userRole') !== 'pemasok') {
                window.location.href = '../../index.php'; // Path ke index.php di root
            }
        });
    </script>
</body>
</html>