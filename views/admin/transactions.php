<?php
require_once __DIR__ . '/../../src/db.php';

$all_transactions = null;

$sql = "
    SELECT
        t.id AS id_transaksi,
        t.tanggal,
        t.barang_id,
        b.nama_barang,
        t.jenis,
        t.kuantitas,
        t.stok_sesudah AS stok_akhir,
        t.pemasok_id,
        p.kontak AS nama_pemasok,
        t.catatan
    FROM transaksi t
    JOIN barang b ON t.barang_id = b.id
    LEFT JOIN pemasok p ON t.pemasok_id = p.id
    ORDER BY t.tanggal DESC
";
if ($conn) { // Check if $conn object exists and is connected
    $all_transactions = $conn->query($sql);

    if ($all_transactions === false) {
        error_log("SQL Error fetching transactions: " . $conn->error);
    }
} else {
    error_log("Database connection failed in transactions controller.");
}

$stmt = "SELECT * FROM barang";
$daftar_barang_result = $conn->query($stmt);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Transaksi - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="bg-gray-100 font-sans flex flex-col min-h-screen">

    <?php include_once 'components/navbar.php' ?>

    <main class="container mx-auto px-6 py-8 flex-grow">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Manajemen Transaksi</h1>
        <p class="text-gray-700 mb-8">Pantau semua transaksi masuk dan keluar barang dari gudang.</p>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Riwayat Transaksi</h2>
                <button id="addTransactionBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">Buat Transaksi Baru</button>
            </div>

            <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="transactionTypeFilter" class="block text-gray-700 text-sm font-semibold mb-1">Jenis Transaksi:</label>
                    <select id="transactionTypeFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <option value="">Semua</option>
                        <option value="masuk">Masuk</option>
                        <option value="keluar">Keluar</option>
                    </select>
                </div>
                <div>
                    <label for="startDateFilter" class="block text-gray-700 text-sm font-semibold mb-1">Dari Tanggal:</label>
                    <input type="date" id="startDateFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                </div>
                <div>
                    <label for="endDateFilter" class="block text-gray-700 text-sm font-semibold mb-1">Sampai Tanggal:</label>
                    <input type="date" id="endDateFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID Transaksi</th>
                            <th class="py-3 px-6 text-left">Tanggal</th>
                            <th class="py-3 px-6 text-left">Nama Barang</th>
                            <th class="py-3 px-6 text-center">Jenis</th>
                            <th class="py-3 px-6 text-center">Kuantitas</th>
                            <th class="py-3 px-6 text-center">Stok Akhir</th>
                            <th class="py-3 px-6 text-left">Oleh User/Pemasok</th>
                            <th class="py-3 px-6 text-left">Catatan</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light" id="transactionTableBody">
                        <?php if ($all_transactions && $all_transactions->num_rows > 0): ?>
                            <?php while ($transaction = $all_transactions->fetch_assoc()): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">
                                        <?= htmlspecialchars($transaction['id_transaksi']) ?>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <?= htmlspecialchars(date('d-m-Y H:i', strtotime($transaction['tanggal']))) ?>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <?= htmlspecialchars($transaction['nama_barang']) ?>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        <?= ($transaction['jenis'] === 'masuk') ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' ?>">
                                            <?= htmlspecialchars(ucfirst($transaction['jenis'])) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <?= htmlspecialchars($transaction['kuantitas']) ?>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <?= htmlspecialchars($transaction['stok_akhir']) ?>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <?= ($transaction['jenis'] === 'masuk' && !empty($transaction['nama_pemasok']))
                                            ? htmlspecialchars($transaction['nama_pemasok'])
                                            : 'Penjualan / Internal' ?>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <?= htmlspecialchars($transaction['catatan']) ?>
                                    </td>
                                    <td class="py-3 px-6 text-center space-x-2">
                                        <a href="../../controllers/admin/hapus_transaksi.php?id=<?= $transaction['id_transaksi'] ?>"
                                            onclick="return confirm('Yakin ingin menghapus transaksi ini? Ini akan mengembalikan stok.');"
                                            class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                            Hapus
                                        </a>

                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="py-6 text-center text-gray-500">
                                    Tidak ada data transaksi ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center mt-6 space-x-2" id="paginationContainer">

            </div>

        </div>
    </main>

    <?php include_once 'components/footer.php'; ?>

    <div id="transactionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-4 md:p-6 rounded-lg shadow-lg w-[90%] max-w-md">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Tambah Transaksi</h2>
            <form id="transactionForm" class="space-y-4">
                <input type="hidden" id="transactionId" name="transactionId">

                <div>
                    <label for="transactionBarangId" class="block text-sm font-medium text-gray-700">Barang</label>
                    <select id="transactionBarangId" name="transactionBarangId" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">-- Pilih Barang --</option>
                        <?php
                        // Check if the query returned any rows
                        if ($daftar_barang_result && mysqli_num_rows($daftar_barang_result) > 0) {
                            while ($barang = mysqli_fetch_assoc($daftar_barang_result)) {
                                // Output each option tag
                                echo '<option value="' . htmlspecialchars($barang['id']) . '" data-stok="' . htmlspecialchars($barang['stok']) . '">'
                                    . htmlspecialchars($barang['nama_barang']) . ' (Stok: ' . htmlspecialchars($barang['stok']) . ')'
                                    . '</option>';
                            }
                        } else {
                            // Optional: Show a message if no items are found
                            echo '<option value="" disabled>Tidak ada barang tersedia</option>';
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label for="transactionJenis" class="block text-sm font-medium text-gray-700">Jenis Transaksi</label>
                    <select id="transactionJenis" name="transactionJenis" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="masuk">Masuk</option>
                        <option value="keluar">Keluar</option>
                    </select>
                </div>

                <div>
                    <label for="transactionKuantitas" class="block text-sm font-medium text-gray-700">Kuantitas</label>
                    <input type="number" id="transactionKuantitas" name="transactionKuantitas" min="1" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>

                <div>
                    <label for="predictedStock" class="block text-sm font-medium text-gray-700">Stok Setelah Transaksi</label>
                    <input type="text" id="predictedStock" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100" readonly value="0">
                </div>


                <div id="pemasokField" class="hidden"> <label for="transactionPemasokId" class="block text-sm font-medium text-gray-700">Pemasok</label>
                    <select id="transactionPemasokId" name="transactionPemasokId" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">-- Pilih Pemasok (Opsional) --</option>
                    </select>
                </div>

                <div>
                    <label for="transactionCatatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea id="transactionCatatan" name="transactionCatatan" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeTransactionModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <script src="js/transaction.js"></script>
</body>

</html>