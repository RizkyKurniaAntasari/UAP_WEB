<?php
session_start();
include '../../src/db.php';
include '../../src/functions.php'; // Pastikan formatRupiah dan getKategori ada di sini

$alertMessage = "Anda belum terdaftar sebagai pemasok, silahkan hubungi admin.";
// Pastikan user sudah login sebagai pemasok
if (!isset($_SESSION['nama']) || !isset($_SESSION['email'])) {
    echo "<script>alert('" . addslashes($alertMessage) . "');</script>";
    header('Location: dashboard.php');
    exit;
}

$cari = "";
if (isset($_POST['cari'])) {
    $cari = mysqli_real_escape_string($conn, $_POST['keyCari']);
}

$nama = $_SESSION['nama'];
$email = $_SESSION['email'];

// Ambil ID Pemasok
$getPemasok = mysqli_prepare($conn, "SELECT id FROM pemasok WHERE kontak = ? AND email = ?");
mysqli_stmt_bind_param($getPemasok, "ss", $nama, $email);
mysqli_stmt_execute($getPemasok);
$resultPemasok = mysqli_stmt_get_result($getPemasok);
$idPemasok = null;
if ($row = mysqli_fetch_assoc($resultPemasok)) {
    $idPemasok = $row['id'];
} else {
    // Jika ID Pemasok tidak ditemukan, bisa jadi user tidak valid atau data di pemasok tidak sinkron.
    // Redirect ke logout atau halaman error
    echo "<script>alert('" . addslashes($alertMessage) . "');</script>";
    header('Location: dashboard.php');
    exit;
}

// Ambil semua kategori untuk dropdown
$list_kategori_query = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");
$list_kategori = [];
while ($cat = mysqli_fetch_assoc($list_kategori_query)) {
    $list_kategori[] = $cat;
}

$editData = null; // Variabel untuk menyimpan data produk yang akan diedit
$isEditMode = false; // Flag untuk menentukan apakah modal edit harus ditampilkan

// Logika untuk menampilkan modal edit (jika ada parameter GET untuk edit)
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $idToEdit = (int)$_GET['id'];
    // Pastikan produk ini milik pemasok yang sedang login
    $getEditProductSql = mysqli_prepare($conn, "SELECT * FROM barang WHERE id = ? AND id_pemasok = ?");
    mysqli_stmt_bind_param($getEditProductSql, "ii", $idToEdit, $idPemasok);
    mysqli_stmt_execute($getEditProductSql);
    $resultEditProduct = mysqli_stmt_get_result($getEditProductSql);
    if ($rowEdit = mysqli_fetch_assoc($resultEditProduct)) {
        $editData = $rowEdit;
        $isEditMode = true; // Set flag untuk menampilkan modal edit
    } else {
        // Jika produk tidak ditemukan atau bukan milik pemasok ini, redirect untuk membersihkan URL
        header("Location: my_products.php");
        exit;
    }
}

// ... (bagian atas kode PHP Anda)

// Tambah Produk
if (isset($_POST['tambahProduk'])) {
    $namaBarang = mysqli_real_escape_string($conn, $_POST['namaProduk']);
    $idKategori = (int) $_POST['kategoriProduk'];

    // Validasi dan konversi untuk stok, harga_beli, harga_jual
    // Jika kosong, kita anggap 0 untuk contoh ini, tapi bisa disesuaikan
    $stok = isset($_POST['stokProduk']) && $_POST['stokProduk'] !== '' ? (int) $_POST['stokProduk'] : 0;
    $satuan = mysqli_real_escape_string($conn, $_POST['satuanProduk']);
    $hargaBeli = isset($_POST['hargaBeliProduk']) && $_POST['hargaBeliProduk'] !== '' ? (int) $_POST['hargaBeliProduk'] : 0;
    $hargaJual = isset($_POST['hargaJualProduk']) && $_POST['hargaJualProduk'] !== '' ? (int) $_POST['hargaJualProduk'] : 0;
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsiProduk']);

    // ... (rest of the INSERT code, which should use prepared statements)
    $insertSql = "INSERT INTO barang 
        (nama_barang, id_kategori, id_pemasok, stok, satuan, harga_beli, harga_jual, deskripsi) 
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertSql);
    // Perhatikan 'i' untuk integer, 's' untuk string
    mysqli_stmt_bind_param($stmt, "siissiis", $namaBarang, $idKategori, $idPemasok, $stok, $satuan, $hargaBeli, $hargaJual, $deskripsi);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: my_products.php?status=added");
        exit;
    } else {
        die("Gagal tambah produk: " . mysqli_error($conn));
    }
}

// Edit Produk
if (isset($_POST['editProdukBtn'])) {
    $idEditProduk = (int) $_POST['idEditProduk'];
    $namaBarang = mysqli_real_escape_string($conn, $_POST['namaProduk']);
    $idKategori = (int) $_POST['kategoriProduk'];

    // Validasi dan konversi untuk stok, harga_beli, harga_jual di bagian edit juga
    $stok = isset($_POST['stokProduk']) && $_POST['stokProduk'] !== '' ? (int) $_POST['stokProduk'] : 0;
    $satuan = mysqli_real_escape_string($conn, $_POST['satuanProduk']);
    $hargaBeli = isset($_POST['hargaBeliProduk']) && $_POST['hargaBeliProduk'] !== '' ? (int) $_POST['hargaBeliProduk'] : 0;
    $hargaJual = isset($_POST['hargaJualProduk']) && $_POST['hargaJualProduk'] !== '' ? (int) $_POST['hargaJualProduk'] : 0;
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsiProduk']);

    // ... (rest of the UPDATE code, which should use prepared statements)
    $updateSql = "UPDATE barang SET
        nama_barang = ?,
        id_kategori = ?,
        stok = ?,
        satuan = ?,
        harga_beli = ?,
        harga_jual = ?,
        deskripsi = ?
        WHERE id = ? AND id_pemasok = ?";
    $stmt = mysqli_prepare($conn, $updateSql);
    // Perhatikan 'i' untuk integer, 's' untuk string
    mysqli_stmt_bind_param($stmt, "siisiissi", $namaBarang, $idKategori, $stok, $satuan, $hargaBeli, $hargaJual, $deskripsi, $idEditProduk, $idPemasok);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: my_products.php?status=updated");
        exit;
    } else {
        die("Gagal update produk: " . mysqli_error($conn));
    }
}

// Hapus Produk
if (isset($_POST['hapusProduk'])) {
    $idHapus = (int) $_POST['idHapusProduk']; // Ganti nama input hidden
    
    // Menggunakan Prepared Statements untuk DELETE
    $deleteSql = "DELETE FROM barang WHERE id = ? AND id_pemasok = ?";
    $stmt = mysqli_prepare($conn, $deleteSql);
    mysqli_stmt_bind_param($stmt, "ii", $idHapus, $idPemasok);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect setelah sukses untuk mencegah form re-submission
        header("Location: my_products.php?status=deleted");
        exit;
    } else {
        die("Gagal hapus produk: " . mysqli_error($conn));
    }
}


// Ambil Data Barang
// Menggunakan Prepared Statements untuk SELECT
$sql = "SELECT 
    b.id,
    b.nama_barang,
    k.nama_kategori,
    b.id_kategori,
    b.stok AS stok_barang,
    b.satuan,
    b.harga_jual,
    b.harga_beli,
    b.deskripsi,
    (
        SELECT t.stok_sesudah 
        FROM transaksi t
        WHERE t.barang_id = b.id
        ORDER BY t.id DESC
        LIMIT 1
    ) AS stok_terakhir_transaksi
FROM 
    barang b
JOIN 
    kategori k ON b.id_kategori = k.id
WHERE 
    b.id_pemasok = ? AND b.nama_barang LIKE ?
ORDER BY 
    b.id DESC";


$stmt = mysqli_prepare($conn, $sql);
$paramCari = "%" . $cari . "%"; // Tambahkan wildcard untuk LIKE
mysqli_stmt_bind_param($stmt, "is", $idPemasok, $paramCari);
mysqli_stmt_execute($stmt);
$dataBarang = mysqli_stmt_get_result($stmt);

$stoks = "SELECT 
    transaksi.stok_sesudah,
    transaksi.id AS transaksi_id,
    pemasok.kontak,
    barang.nama_barang
FROM 
    transaksi
JOIN 
    pemasok ON transaksi.pemasok_id = pemasok.id
JOIN 
    barang ON barang.id_pemasok = pemasok.id
WHERE 
    pemasok.id = $idPemasok;
";
// If you choose this, ensure $idPemasok is absolutely safe (e.g., cast to int)
// For example: $idPemasok = (int)$_SESSION['id_pemasok'];
$stmt1 = mysqli_prepare($conn, $stoks);
// No mysqli_stmt_bind_param needed here
mysqli_stmt_execute($stmt1);
$dataStoks = mysqli_stmt_get_result($stmt1);

// Handle flash messages
$flashMessage = '';
$flashMessageType = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'added') {
        $flashMessage = 'Produk berhasil ditambahkan!';
        $flashMessageType = 'success';
    } elseif ($_GET['status'] === 'updated') {
        $flashMessage = 'Produk berhasil diperbarui!';
        $flashMessageType = 'success';
    } elseif ($_GET['status'] === 'deleted') {
        $flashMessage = 'Produk berhasil dihapus!';
        $flashMessageType = 'success';
    }
    // Hapus parameter status dari URL untuk mencegah pesan muncul lagi setelah refresh
    // header("Location: my_products.php"); // Ini akan menghapus flash message jika tidak ditangani dengan JS
    // exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Saya - Pemasok Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col min-h-screen font-sans bg-gray-100">

    <nav class="p-4 text-white bg-green-700 shadow-md">
        <div class="container flex items-center justify-between mx-auto">
            <a href="dashboard.php" class="text-2xl font-bold">Pemasok Dashboard</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:text-green-200">Beranda</a>
                <a href="my_products.php" class="font-semibold hover:text-green-200">Produk Saya</a>
                <a href="orders.php" class="hover:text-green-200">Pesanan</a>
                <a href="../../logout.php" class="px-3 py-1 transition duration-300 bg-red-600 rounded-md hover:bg-red-700">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container flex-grow px-6 py-8 mx-auto">
        <h1 class="mb-6 text-4xl font-bold text-gray-800">Produk yang Saya Sediakan</h1>
        <p class="mb-8 text-gray-700">Daftar barang yang Anda sediakan untuk sistem inventaris.</p>

        <div class="p-6 mb-8 bg-white rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Daftar Produk</h2>
                <button id="addProductBtn" class="px-4 py-2 text-white transition duration-300 bg-green-600 rounded-md hover:bg-green-700">Tambah Produk Baru</button>
            </div>

            <div class="mb-4">
                <form action="" method="post">
                    <input type="text" name="keyCari" id="searchInput" placeholder="Cari nama produk..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 md:w-1/3" value="<?= htmlspecialchars($cari) ?>">
                    <button type="submit" class="hidden" name="cari"></button>
                </form>
            </div>

            <div id="flashMessage" class="relative px-4 py-3 mb-4 rounded 
                <?php 
                    if (!empty($flashMessage)) { 
                        echo 'flex'; // Show if message exists
                        if ($flashMessageType === 'success') {
                            echo ' text-green-700 bg-green-100 border border-green-400';
                        } elseif ($flashMessageType === 'error') {
                            echo ' text-red-700 bg-red-100 border border-red-400';
                        }
                    } else {
                        echo ' hidden'; // Hide if no message
                    }
                ?>" role="alert">
                <span id="flashMessageText" class="block sm:inline"><?= htmlspecialchars($flashMessage) ?></span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.classList.add('hidden'); window.history.replaceState({}, document.title, window.location.pathname);">
                    <svg class="w-6 h-6 fill-current 
                        <?php 
                            if ($flashMessageType === 'success') echo 'text-green-500'; 
                            elseif ($flashMessageType === 'error') echo 'text-red-500'; 
                        ?>" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.697l-2.651 2.652a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.651 7.348a1.2 1.2 0 1 1 1.697-1.697L10 8.303l2.651-2.652a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z" />
                    </svg>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="text-sm leading-normal text-gray-700 uppercase bg-gray-200">
                            <th class="px-6 py-3 text-left">No</th>
                            <th class="px-6 py-3 text-left">Nama Barang</th>
                            <th class="px-6 py-3 text-left">Kategori</th>
                            <th class="px-6 py-3 text-center">Stok Tersedia</th>
                            <th class="px-6 py-3 text-center">Stok Akhir</th>
                            <th class="px-6 py-3 text-right">Harga Satuan</th>
                            <th class="px-6 py-3 text-left">Deskripsi</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-light text-gray-600" id="productTableBody">
                        <?php if (mysqli_num_rows($dataBarang) == 0) { ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Produk tidak ditemukan.
                                </td>
                            </tr>
                        <?php } else {
                            $no = 1;
                            while ($barang = mysqli_fetch_assoc($dataBarang) ) { ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="px-6 py-3 text-left whitespace-nowrap"><?= $no++ ?></td>
                                    <td class="px-6 py-3 text-left"><?= htmlspecialchars($barang['nama_barang']) ?></td>
                                    <td class="px-6 py-3 text-left"><?= htmlspecialchars($barang['nama_kategori']) ?></td>
                                    <td class="px-6 py-3 text-center"><?= htmlspecialchars($barang['stok_barang']) ?></td>
                                    <td class="px-6 py-3 text-center"><?= htmlspecialchars($barang['stok_terakhir_transaksi'] ?? '0') ?></td> 
                                    <td class="px-6 py-3 text-right"><?= formatRupiah($barang['harga_jual']) ?></td>
                                    <td class="px-6 py-3 text-left"><?= htmlspecialchars($barang['deskripsi']) ?></td>
                                    <td class="px-6 py-3 text-center">
                                        <div class="flex justify-center space-x-2 item-center">
                                            <a href="?action=edit&id=<?= htmlspecialchars($barang['id']) ?>" class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Edit">
                                                ✏️
                                            </a>
                                            <form action="" method="post" onsubmit="return confirm('Anda yakin ingin menghapus produk ini?');">
                                                <input type="hidden" name="idHapusProduk" value="<?= htmlspecialchars($barang['id']) ?>">
                                                <button type="submit" name="hapusProduk" class="w-6 h-6 transform hover:text-red-500 hover:scale-110" title="Hapus">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                        <?php }
                        } ?>
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

    <div id="productModal" class="fixed inset-0 items-center justify-center hidden bg-gray-600 bg-opacity-50">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl">
            <h3 id="modalTitleAdd" class="mb-4 text-2xl font-bold">Tambah Produk Baru</h3>
            <form action="" method="post">
                <div class="mb-4">
                    <label for="productNameAdd" class="block mb-2 text-sm font-bold text-gray-700">Nama Produk:</label>
                    <input type="text" id="productNameAdd" name="namaProduk" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="productCategoryAdd" class="block mb-2 text-sm font-bold text-gray-700">Kategori:</label>
                    <select id="productCategoryAdd" name="kategoriProduk" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required>
                        <?php foreach ($list_kategori as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['id']); ?>"><?php echo htmlspecialchars($cat['nama_kategori']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="productStockAdd" class="block mb-2 text-sm font-bold text-gray-700">Stok Tersedia:</label>
                    <input type="number" id="productStockAdd" name="stokProduk" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required min="0">
                </div>
                <div class="mb-4">
                    <label for="satuanAdd" class="block mb-2 text-sm font-bold text-gray-700">Satuan:</label>
                    <input type="text" id="satuanAdd" name="satuanProduk" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="hargaBeliAdd" class="block mb-2 text-sm font-bold text-gray-700">Harga Beli (Rp):</label>
                    <input type="number" id="hargaBeliAdd" name="hargaBeliProduk" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required min="0">
                </div>
                <div class="mb-4">
                    <label for="hargaJualAdd" class="block mb-2 text-sm font-bold text-gray-700">Harga Jual (Rp):</label>
                    <input type="number" id="hargaJualAdd" name="hargaJualProduk" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required min="0">
                </div>
                <div class="mb-4">
                    <label for="productDescAdd" class="block mb-2 text-sm font-bold text-gray-700">Deskripsi Produk:</label>
                    <textarea type="text" id="productDescAdd" name="deskripsiProduk" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required rows="3"></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancelModalBtnAdd" class="px-4 py-2 text-gray-800 bg-gray-300 rounded-md hover:bg-gray-400">Batal</button>
                    <button type="submit" name="tambahProduk" class="px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editProdukModal" class="fixed inset-0 items-center justify-center <?= $isEditMode ? 'flex' : 'hidden' ?> bg-gray-600 bg-opacity-50">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl">
            <h3 id="modalTitleEdit" class="mb-4 text-2xl font-bold">Edit Produk</h3>
            <form action="" method="post">
                <input type="hidden" name="idEditProduk" value="<?= htmlspecialchars($editData['id'] ?? '') ?>">
                
                <div class="mb-4">
                    <label for="namaProdukEdit" class="block mb-2 text-sm font-bold text-gray-700">Nama Produk:</label>
                    <input type="text" id="namaProdukEdit" name="namaProduk" value="<?= htmlspecialchars($editData['nama_barang'] ?? '') ?>" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="kategoriProdukEdit" class="block mb-2 text-sm font-bold text-gray-700">Kategori:</label>
                    <select id="kategoriProdukEdit" name="kategoriProduk" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required>
                        <?php foreach ($list_kategori as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['id']); ?>" 
                                <?= (isset($editData['id_kategori']) && $editData['id_kategori'] == $cat['id']) ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="stokProdukEdit" class="block mb-2 text-sm font-bold text-gray-700">Stok Tersedia:</label>
                    <input type="number" id="stokProdukEdit" name="stokProduk" value="<?= htmlspecialchars($editData['stok'] ?? '') ?>" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required min="0">
                </div>
                <div class="mb-4">
                    <label for="satuanProdukEdit" class="block mb-2 text-sm font-bold text-gray-700">Satuan:</label>
                    <input type="text" id="satuanProdukEdit" name="satuanProduk" value="<?= htmlspecialchars($editData['satuan'] ?? '') ?>" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="hargaBeliProdukEdit" class="block mb-2 text-sm font-bold text-gray-700">Harga Beli (Rp):</label>
                    <input type="number" id="hargaBeliProdukEdit" name="hargaBeliProduk" value="<?= htmlspecialchars($editData['harga_beli'] ?? '') ?>" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required min="0">
                </div>
                <div class="mb-4">
                    <label for="hargaJualProdukEdit" class="block mb-2 text-sm font-bold text-gray-700">Harga Jual (Rp):</label>
                    <input type="number" id="hargaJualProdukEdit" name="hargaJualProduk" value="<?= htmlspecialchars($editData['harga_jual'] ?? '') ?>" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required min="0">
                </div>
                <div class="mb-4">
                    <label for="deskripsiProdukEdit" class="block mb-2 text-sm font-bold text-gray-700">Deskripsi Produk:</label>
                    <textarea type="text" id="deskripsiProdukEdit" name="deskripsiProduk" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" required rows="3"><?= htmlspecialchars($editData['deskripsi'] ?? '') ?></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancelModalBtnEdit" class="px-4 py-2 text-gray-800 bg-gray-300 rounded-md hover:bg-gray-400">Batal</button>
                    <button type="submit" name="editProdukBtn" class="px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

<script src="js/my_products.js"></script>
</body>

</html> 