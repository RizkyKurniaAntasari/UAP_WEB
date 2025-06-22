<?php
session_start();
include_once __DIR__ . '/../../src/db.php';

// Validasi login user
// This block ensures only logged-in users proceed.
if (!isset($_SESSION['nama']) || !isset($_SESSION['email'])) {
    header("Location: ../../login.php");
    exit;
}

$nama = $_SESSION['nama'];
$email = $_SESSION['email'];

// Ambil ID Pemasok
$getPemasok = mysqli_prepare($conn, "SELECT id FROM pemasok WHERE kontak = ? AND email = ?");
if (!$getPemasok) {
    // Handle prepare statement error
    error_log("Failed to prepare statement for fetching supplier ID: " . mysqli_error($conn));
    header("Location: dashboard.php?error=db_error"); // Redirect to dashboard with an error
    exit;
}
mysqli_stmt_bind_param($getPemasok, "ss", $nama, $email);
mysqli_stmt_execute($getPemasok);
$resultPemasok = mysqli_stmt_get_result($getPemasok);

$idPemasok = null;
if ($row = mysqli_fetch_assoc($resultPemasok)) {
    $idPemasok = $row['id'];
} else {
    // If no supplier ID is found for the logged-in user,
    // it implies an inconsistency or that the user isn't a supplier.
    // Redirect them to their dashboard or login, with a message.
    header("Location: dashboard.php?message=supplier_not_found");
    exit; // Crucial: Stop script execution after redirect
}

// Close the prepared statement for getPemasok
mysqli_stmt_close($getPemasok);

// Query transaksi dan barang
$sql = "SELECT
            t.id, t.tanggal, t.kuantitas, t.jenis,
            b.nama_barang, b.harga_jual, b.harga_beli
        FROM transaksi t
        JOIN barang b ON t.barang_id = b.id
        WHERE b.id_pemasok = ?";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    // Handle prepare statement error for transactions
    error_log("Failed to prepare statement for fetching transactions: " . mysqli_error($conn));
    header("Location: dashboard.php?error=db_query_failed"); // Redirect with an error
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $idPemasok);
mysqli_stmt_execute($stmt);
$dataBarang = mysqli_stmt_get_result($stmt);

// Close the prepared statement for transactions
mysqli_stmt_close($stmt);

// Don't forget to close the database connection when done
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Pemasok Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Gaya tambahan untuk modal */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .modal-content {
            z-index: 1000;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen font-sans bg-gray-100">
    <nav class="p-4 text-white bg-green-700 shadow-md">
        <div class="container flex items-center justify-between mx-auto">
            <a href="dashboard.php" class="text-2xl font-bold">Pemasok Dashboard</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:text-green-200">Beranda</a>
                <a href="my_products.php" class="hover:text-green-200">Produk Saya</a>
                <a href="orders.php" class="font-semibold hover:text-green-200">Pesanan</a>
                <a href="../../logout.php" class="px-3 py-1 transition duration-300 bg-red-600 rounded-md hover:bg-red-700" onclick="logoutClientSide(event)">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container flex-grow px-6 py-8 mx-auto">
        <h1 class="mb-6 text-4xl font-bold text-gray-800">Daftar Pesanan</h1>
        <p class="mb-8 text-gray-700">Lihat pesanan yang melibatkan produk Anda dan jenisnya.</p>

        <div class="p-6 mb-8 bg-white rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Pesanan Masuk</h2>
            </div>

            <form action="" method="post" class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                <div>
                    <label for="inputJenis" class="block mb-1 text-sm font-semibold text-gray-700">Jenis Pesanan:</label>
                    <select name="inputJenis" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Semua Jenis</option>
                        <option value="masuk">Barang Masuk</option>
                        <option value="keluar">Barang Keluar</option>
                    </select>
                </div>
                <div>
                    <label for="inputNama" class="block mb-1 text-sm font-semibold text-gray-700">Cari Pesanan:</label>
                    <input type="text" name="inputNama" placeholder="Cari Nama Produk..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="text-right md:col-span-2">
                    <button type="submit" name="cariPesanan" class="px-4 py-2 text-white transition duration-300 bg-indigo-600 rounded-md hover:bg-indigo-700">Cari Pesanan</button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="text-sm leading-normal text-gray-700 uppercase bg-gray-200">
                            <th class="px-6 py-3 text-left">ID Pesanan</th>
                            <th class="px-6 py-3 text-left">Tanggal</th>
                            <th class="px-6 py-3 text-left">Nama Barang</th>
                            <th class="px-6 py-3 text-center">Kuantitas</th>
                            <th class="px-6 py-3 text-right text-red-700">Total Keluar</th>
                            <th class="px-6 py-3 text-right text-green-700">Total Masuk</th>
                            <th class="px-6 py-3 text-center">Jenis</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-light text-gray-600" id="orderTableBody">
                        <?php if (mysqli_num_rows($dataBarang) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($dataBarang)): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-6 py-3 text-left"><?= htmlspecialchars($row['id']) ?></td>
                                    <td class="px-6 py-3 text-left"><?= htmlspecialchars($row['tanggal']) ?></td>
                                    <td class="px-6 py-3 text-left"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td class="px-6 py-3 text-center"><?= htmlspecialchars($row['kuantitas']) ?></td>

                                    <?php if ($row['jenis'] === 'keluar'): ?>
                                        <!-- Barang Keluar -->
                                        <td class="px-6 py-3 text-right text-red-700 font-semibold">
                                            - Rp<?= number_format($row['kuantitas'] * $row['harga_jual']) ?>
                                        </td>
                                        <td class="px-6 py-3 text-right">-</td>
                                    <?php elseif ($row['jenis'] === 'masuk'): ?>
                                        <!-- Barang Masuk -->
                                        <td class="px-6 py-3 text-right">-</td>
                                        <td class="px-6 py-3 text-right text-green-700 font-semibold">
                                            + Rp<?= number_format($row['kuantitas'] * $row['harga_beli']) ?>
                                        </td>
                                    <?php else: ?>
                                        <td class="px-6 py-3 text-right">-</td>
                                        <td class="px-6 py-3 text-right">-</td>
                                    <?php endif; ?>

                                    <td class="px-6 py-3 text-center">
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                    <?= match ($row['jenis']) {
                                    'keluar' => 'bg-red-100 text-red-800',
                                    'masuk' => 'bg-green-100 text-green-800',
                                    default => 'bg-gray-100 text-gray-800',
                                } ?>">
                                            <?= ucfirst($row['jenis']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-center text-sm text-gray-500 italic">-</td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada transaksi ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center mt-6 space-x-2" id="paginationContainer">
            </div>

        </div>
    </main>

    <footer class="py-4 mt-8 text-center text-white bg-gray-800">
        <div class="container px-6 mx-auto">
            <p class="text-sm">&copy; 2025 Sistem Inventory. Hak Cipta Dilindungi.</p>
        </div>
    </footer>
</body>

</html>