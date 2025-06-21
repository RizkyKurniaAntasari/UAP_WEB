<?php
include_once __DIR__ . '/../../src/db.php';

$stmt = mysqli_prepare($conn, "SELECT COUNT(id) FROM barang");
$stmt2 = mysqli_prepare($conn, "SELECT COUNT(id) FROM pemasok");

if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($result); // Use mysqli_fetch_array to access by index
$banyak_barang = $row[0];
mysqli_stmt_close($stmt);

mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);
$column = mysqli_fetch_array($result2);
$banyak_pemasok= $column[0];
mysqli_stmt_close($stmt2);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans flex flex-col min-h-screen">

    <?php include_once 'components/navbar.php'?>

    <main class="container mx-auto px-6 py-8 flex-grow"> <h1 class="text-4xl font-bold text-gray-800 mb-6">Selamat Datang, Admin!</h1>
        <p class="text-gray-700 mb-8">Ini adalah pusat kontrol Anda untuk mengelola seluruh sistem inventaris.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Daftar Barang</h3>
                <p class="text-4xl font-bold text-blue-600"><?= $banyak_barang ?></p>
                <p class="text-gray-500">unit tersedia</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Transaksi Hari Ini</h3>
                <p class="text-4xl font-bold text-green-600">45</p>
                <p class="text-gray-500">transaksi masuk/keluar</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Pemasok Terdaftar</h3>
                <p class="text-4xl font-bold text-purple-600"><?= $banyak_pemasok ?></p>
                <p class="text-gray-500">pemasok aktif</p>
            </div>
        </div>

        <div class="mt-10">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Aksi Cepat</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="products.php" class="bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition duration-300 text-center">Tambah Barang Baru</a>
                <a href="transactions.php" class="bg-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300 text-center">Buat Transaksi</a>
                <a href="transactions.php" class="bg-teal-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-teal-700 transition duration-300 text-center">Lihat Laporan</a>
            </div>
        </div>
    </main>

    <?php include_once 'components/footer.php'?>


</body>
</html>