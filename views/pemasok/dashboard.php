<?php
session_start();
include '../../src/db.php';

if (isset($_SESSION['loggedin'])) {
    $nama = $_SESSION['nama'];
    $id = $_SESSION['id'];
    // echo "<script>alert('{$_SESSION['id']}')</script>";
    
    $sql = "SELECT * FROM barang WHERE id_pemasok = '$id'";
    $dataBarang = mysqli_query($conn, $sql);
    
    
    $jumlahProduk = $dataBarang->num_rows;
    $pesananBaru = 5;
    
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemasok - Sistem Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col min-h-screen font-sans bg-gray-100"> <nav class="p-4 text-white bg-green-700 shadow-md">
        <div class="container flex items-center justify-between mx-auto">
            <a href="dashboard.php" class="text-2xl font-bold">Pemasok Dashboard</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:text-green-200">Beranda</a>
                <a href="my_products.php" class="hover:text-green-200">Produk Saya</a>
                <a href="orders.php" class="hover:text-green-200">Pesanan</a>
                <a href="../../logout.php" class="px-3 py-1 transition duration-300 bg-red-600 rounded-md hover:bg-red-700" onclick="logoutClientSide(event)">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container flex-grow px-6 py-8 mx-auto"> <h1 class="mb-6 text-4xl font-bold text-gray-800">Selamat Datang, <?=$nama?>!</h1>
        <p class="mb-8 text-gray-700">Di sini Anda dapat melihat informasi terkait produk yang Anda sediakan dan status pesanan.</p>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="p-6 transition duration-300 bg-white rounded-lg shadow-md hover:shadow-lg">
                <h3 class="mb-2 text-xl font-semibold text-gray-700">Produk Anda Terdaftar</h3>
                <p class="text-4xl font-bold text-green-600"><?= $jumlahProduk ?></p>
                <p class="text-gray-500">jenis produk</p>
            </div>
            <div class="p-6 transition duration-300 bg-white rounded-lg shadow-md hover:shadow-lg">
                <h3 class="mb-2 text-xl font-semibold text-gray-700">Pesanan Baru</h3>
                <p class="text-4xl font-bold text-blue-600"><?= $pesananBaru ?></p>
                <p class="text-gray-500">menunggu konfirmasi</p>
            </div>
        </div>

        <div class="mt-10">
            <h2 class="mb-4 text-3xl font-bold text-gray-800">Aksi Cepat</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <a href="my_products.php" class="px-6 py-3 font-semibold text-center text-white transition duration-300 bg-green-600 rounded-lg hover:bg-green-700">Lihat Produk Saya</a>
                <a href="orders.php" class="px-6 py-3 font-semibold text-center text-white transition duration-300 bg-indigo-600 rounded-lg hover:bg-indigo-700">Lihat Pesanan</a>
            </div>
        </div>
    </main>

    <footer class="py-4 mt-8 text-center text-white bg-gray-800"> <div class="container px-6 mx-auto">
            <p class="text-sm">&copy; 2025 Sistem Inventory. Hak Cipta Dilindungi.</p>
        </div>
    </footer>
</body>
</html>