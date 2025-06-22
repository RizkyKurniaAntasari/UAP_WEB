<?php
session_start();
    include_once __DIR__ . '/../../src/db.php';
    include_once __DIR__ . '/../../src/functions.php';
    if($_SESSION['role'] != 'admin'){
    keluar_bang();
}
    $categories = $conn->query("SELECT * FROM kategori ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="bg-gray-100 font-sans flex min-h-screen flex-col">

    <?php include_once 'components/navbar.php' ?>

    <main class="container mx-auto px-6 py-8 flex-grow">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Manajemen Kategori</h1>
        <p class="text-gray-700 mb-8">Kelola kategori untuk pengelompokan barang.</p>

        <?php
        // Display status messages from redirects
        if (isset($_GET['status'])) {
            $status = htmlspecialchars($_GET['status']);
            $message = '';
            $class = 'bg-green-100 border-green-400 text-green-700';
            switch ($status) {
                case 'added':
                    $message = 'Kategori berhasil ditambahkan!';
                    break;
                case 'edited':
                    $message = 'Kategori berhasil diperbarui!';
                    break;
                case 'deleted':
                    $message = 'Kategori berhasil dihapus!';
                    break;
                default:
                    $message = 'Operasi berhasil.';
                    break;
            }
            echo "<div class='p-4 mb-4 text-sm rounded-lg border {$class}' role='alert'>{$message}</div>";
        }
        if (isset($_GET['error'])) {
            $error = htmlspecialchars($_GET['error']);
            $message = 'Terjadi kesalahan saat melakukan operasi.';
            $class = 'bg-red-100 border-red-400 text-red-700';
            switch ($error) {
                case 'nama_kategori_empty':
                    $message = 'Nama kategori tidak boleh kosong.';
                    break;
                case 'update_failed':
                    $message = 'Gagal memperbarui kategori.';
                    break;
                case 'add_failed':
                    $message = 'Gagal menambahkan kategori.';
                    break;
                case 'delete_failed':
                    $message = 'Gagal menghapus kategori.';
                    break;
                case 'prepare_failed':
                case 'prepare_failed_delete':
                    $message = 'Kesalahan sistem saat menyiapkan operasi database.';
                    break;
                default:
                    $message = 'Terjadi kesalahan yang tidak diketahui.';
                    break;
            }
            echo "<div class='p-4 mb-4 text-sm rounded-lg border {$class}' role='alert'>{$message}</div>";
        }
        ?>

        <div class="bg-white p-6 rounded shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Daftar Kategori</h2>
                <button onclick="openAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">Tambah Kategori Baru</button>
            </div>

            <div class="mb-4">
                <input type="text" id="categorySearch" placeholder="Cari kategori..." class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-3 px-6">ID</th>
                            <th class="py-3 px-6">Nama Kategori</th>
                            <th class="py-3 px-6">Deskripsi</th>
                            <th class="py-3 px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light text-center" id="categoryTableBody">
                        <?php if ($categories && $categories->num_rows > 0) : ?>
                            <?php while ($row = $categories->fetch_assoc()) : ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6  whitespace-nowrap"><?= htmlspecialchars($row['id']) ?></td>
                                    <td class="py-3 px-6 "><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                    <td class="py-3 px-6 "><?= htmlspecialchars($row['deskripsi']) ?></td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-3">
                                            <button onclick='openEditModal(<?= json_encode($row) ?>)' class="text-blue-600 hover:underline font-normal">Edit</button>
                                            <a href="../../controllers/admin/categories.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus kategori ini?')" class="text-red-600 hover:underline font-normal">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="py-4 px-6 text-center text-gray-500">
                                    <?php echo isset($error_message) ? $error_message : "Tidak ada kategori yang ditemukan."; ?>
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
    <?php include_once 'components/footer.php' ?>

    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-md shadow-lg w-full max-w-md relative">
            <span class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl cursor-pointer" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800 mb-6">Tambah Kategori Baru</h2>
            <form id="categoryForm" method="POST" action="../../controllers/admin/categories.php"> <input type="hidden" name="id" id="categoryId">
                <div class="mb-4">
                    <label for="categoryName" class="block text-gray-700 text-sm font-semibold mb-2">Nama Kategori</label>
                    <input type="text" id="categoryName" name="nama_kategori" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-6">
                    <label for="categoryDescription" class="block text-gray-700 text-sm font-semibold mb-2">Deskripsi</label>
                    <textarea id="categoryDescription" name="deskripsi" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-800 px-5 py-2 rounded-md hover:bg-gray-400 transition duration-300">Batal</button>
                    <button type="submit" name="submit_category" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition duration-300">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/categories.js"></script>
</body>

</html>