<?php
session_start();
// Pastikan path ke db.php sudah benar, relatif dari lokasi file ini
include '../../src/db.php';

$nama = "Tamu";
$jumlahProduk = 0;
$pesananBaru = 0;

// Flag untuk status login
$isLoggedIn = false;
$alertMessage = "Anda belum terdaftar sebagai pemasok, silahkan hubungi admin.";

if (isset($_SESSION['role']) && $_SESSION['role'] == 'unknown') {
    echo "<script>alert('" . addslashes($alertMessage) . "');</script>";
}

// Periksa status login
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $isLoggedIn = true; // Set flag menjadi true karena pengguna login

    // Jika login, baru ambil data dari session dan database
    if (isset($_SESSION['nama'])) {
        $nama = htmlspecialchars($_SESSION['nama']); // Sanitasi dan gunakan nama dari session
    }

    $email = $_SESSION['email'];

    // Ambil ID Pemasok
    $getPemasok = mysqli_prepare($conn, "SELECT id FROM pemasok WHERE kontak = ? AND email = ?");
    mysqli_stmt_bind_param($getPemasok, "ss", $nama, $email);
    mysqli_stmt_execute($getPemasok);
    $resultPemasok = mysqli_stmt_get_result($getPemasok);
    $idPemasok = null;
    if ($row = mysqli_fetch_assoc($resultPemasok)) {
        $idPemasok = $row['id'];
    }

    if (isset($_SESSION['id']) && $conn) { // Pastikan $conn ada sebelum query database
        $id = $_SESSION['id']; // ID pemasok

        // Ambil jumlah produk yang terkait dengan pemasok ini
        $stmt_produk = $conn->prepare("SELECT COUNT(*) AS jumlah_produk FROM barang WHERE id_pemasok = ?");
        if ($stmt_produk) {
            $stmt_produk->bind_param("i", $idPemasok);
            $stmt_produk->execute();
            $result_produk = $stmt_produk->get_result();
            $row_produk = $result_produk->fetch_assoc();
            $jumlahProduk = $row_produk['jumlah_produk'];
            $stmt_produk->close();
        } else {
            // Log error jika prepared statement gagal
            error_log("Prepare statement failed for counting products: " . $conn->error);
        }

        $trs = $conn->prepare("SELECT COUNT(*) AS trs FROM transaksi WHERE pemasok_id = ?");

        $trs->bind_param("i", $idPemasok);
        $trs->execute();
        $res = $trs->get_result();
        $row = $res->fetch_assoc();
        $pesananBaru = $row['trs'];
        $trs->close();

    } elseif (!isset($_SESSION['id'])) {
        // Log jika session ID tidak ada padahal loggedin true (kasus aneh, tapi bagus untuk debug)
        error_log("User logged in but " . $_SESSION['id'] . " is not set.");
    }
} else {
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

<body class="flex flex-col min-h-screen font-sans bg-gray-100">
    <nav class="p-4 text-white bg-green-700 shadow-md">
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

    <main class="container flex-grow px-6 py-8 mx-auto">
        <h1 class="mb-6 text-4xl font-bold text-gray-800">Selamat Datang, <?= $nama ?>!</h1>
        <p class="mb-8 text-gray-700">Di sini Anda dapat melihat informasi terkait produk yang Anda sediakan dan status pesanan.</p>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="p-6 transition duration-300 bg-white rounded-lg shadow-md hover:shadow-lg">
                <h3 class="mb-2 text-xl font-semibold text-gray-700">Produk Anda Terdaftar</h3>
                <p class="text-4xl font-bold text-green-600"><?= $jumlahProduk ?></p>
                <p class="text-gray-500">jenis produk</p>
            </div>
            <div class="p-6 transition duration-300 bg-white rounded-lg shadow-md hover:shadow-lg">
                <h3 class="mb-2 text-xl font-semibold text-gray-700">Barang Masuk</h3>
                <p class="text-4xl font-bold text-blue-600"><?= $pesananBaru ?></p>
                <p class="text-gray-500">cek pesanan untuk detail</p>
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

    <footer class="py-4 mt-8 text-center text-white bg-gray-800">
        <div class="container px-6 mx-auto">
            <p class="text-sm">&copy; 2025 Sistem Inventory. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script>
        <?php if (!$isLoggedIn): ?>
            alert("Anda belum login. Silakan login terlebih dahulu.");
            // window.location.href = '../../login.php'; 
        <?php endif; ?>

        function logoutClientSide(event) {
            // Ini adalah placeholder, logout sebenarnya akan ditangani oleh logout.php
            console.log("Logging out...");
            // event.preventDefault(); 
            // window.location.href = '../../logout.php'; 
        }
    </script>
</body>

</html>