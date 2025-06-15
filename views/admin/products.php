<?php
// views/admin/products.php

// You might not even need this line here if db.php is only used by the controller.
// If your checkLogin() or checkRole() functions (if they exist in db.php) are needed directly in the view, keep it.
// Otherwise, the controller already includes db.php.
// include_once __DIR__ . '/../../src/db.php';

// Include the products controller. This file is responsible for populating:
// $barang_list (with nama_kategori and nama_pemasok via JOINs)
// $kategori_options
// $pemasok_options
include_once __DIR__ . '/../../controllers/admin/products.php';

// At this point, $barang_list, $kategori_options, and $pemasok_options are correctly populated
// by the logic within controllers/admin/products.php.
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="bg-gray-100 font-sans flex flex-col min-h-screen">

    <?php
    // Include the navigation bar
    include_once 'components/navbar.php';
    ?>

    <main class="container mx-auto px-6 py-8 flex-grow">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Manajemen Barang</h1>
        <p class="text-gray-700 mb-8">Kelola data barang, stok, kategori, dan pemasok.</p>

        <?php if (isset($_COOKIE['flash_message'])): ?>
            <div id="flash-message" class="relative px-4 py-3 rounded-lg shadow-md mb-6
                <?php echo ($_COOKIE['flash_type'] ?? 'success') === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>"
                role="alert">
                <strong class="font-bold"><?php echo ($_COOKIE['flash_type'] ?? 'success') === 'success' ? 'Sukses!' : 'Error!'; ?></strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($_COOKIE['flash_message']); ?></span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="document.getElementById('flash-message').style.display='none';">
                    <svg class="fill-current h-6 w-6 text-<?php echo ($_COOKIE['flash_type'] ?? 'success') === 'success' ? 'green' : 'red'; ?>-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.103l-2.651 3.746a1.2 1.2 0 0 1-1.697-1.697l3.746-2.651-3.746-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.897l2.651-3.746a1.2 1.2 0 0 1 1.697 1.697L11.103 10l3.746 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
            <?php
            // Clear the cookies after displaying
            setcookie('flash_message', '', time() - 3600, '/');
            setcookie('flash_type', '', time() - 3600, '/');
            ?>
        <?php endif; ?>


        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Daftar Barang</h2>
                <button onclick="openAddProductModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">Tambah Barang Baru</button>
            </div>

            <div class="mb-4 flex flex-col md:flex-row items-center space-y-3 md:space-y-0 md:space-x-4">
                <input type="text" id="productSearch" placeholder="Cari nama barang..." class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">
                <select id="categoryFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/4">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategori_options as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['id']); ?>"><?php echo htmlspecialchars($cat['nama_kategori']); ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="supplierFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/4">
                    <option value="">Semua Pemasok</option>
                    <?php foreach ($pemasok_options as $pem): ?>
                        <option value="<?php echo htmlspecialchars($pem['id']); ?>"><?php echo htmlspecialchars($pem['kontak']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 ">ID</th>
                            <th class="py-3 px-6 ">Nama Barang</th>
                            <th class="py-3 px-6 ">Kategori</th>
                            <th class="py-3 px-6 ">Pemasok</th>
                            <th class="py-3 px-6 ">Stok</th>
                            <th class="py-3 px-6 ">Harga Satuan</th>
                            <th class="py-3 px-6 ">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light" id="productTableBody">
                        <?php if (empty($barang_list)): ?>
                            <tr>
                                <td colspan="7" class="py-5 px-6 text-center text-gray-500">Belum ada data barang.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($barang_list as $barang): ?>
                                <tr class="border-b text-center border-gray-200 hover:bg-gray-100"
                                    data-id="<?php echo htmlspecialchars($barang['id']); ?>"
                                    data-category-id="<?php echo htmlspecialchars($barang['id_kategori']); ?>"
                                    data-supplier-id="<?php echo htmlspecialchars($barang['id_pemasok']); ?>"
                                    data-product-name="<?php echo htmlspecialchars(strtolower($barang['nama_barang'])); ?>">
                                    <td class="py-3 px-6  whitespace-nowrap"><?php echo htmlspecialchars($barang['id']); ?></td>
                                    <td class="py-3 px-6 "><?php echo htmlspecialchars($barang['nama_barang']); ?></td>
                                    <td class="py-3 px-6 "><?php echo htmlspecialchars($barang['nama_kategori'] ?: 'N/A'); ?></td>
                                    <td class="py-3 px-6 "><?php echo htmlspecialchars($barang['nama_pemasok'] ?: 'N/A'); ?></td>
                                    <td class="py-3 px-6 text-center">
                                        <span class="font-bold <?php echo $barang['stok'] <= 10 ? ($barang['stok'] == 0 ? 'text-red-600' : 'text-yellow-600') : ''; ?>">
                                            <?php echo htmlspecialchars($barang['stok']); ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">Rp <?php echo number_format(htmlspecialchars($barang['harga_jual']), 0, ',', '.'); ?></td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-2">
                                            <button onclick="openEditProductModal(<?php echo htmlspecialchars(json_encode($barang)); ?>)" class="text-blue-500 font-normal py-1 px-3 rounded-md shadow-sm transition-colors duration-200">Edit</button>
                                            <form action="../../controllers/admin/products.php" method="POST" onsubmit="return confirmDelete()">
                                                <input type="hidden" name="action" value="delete_barang">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($barang['id']); ?>">
                                                <button type="submit" class="text-red-500 font-normal py-1 px-3 rounded-md shadow-sm transition-colors duration-200">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center mt-6 space-x-2" id="paginationContainer">
                </div>
        </div>
    </main>

    <?php
    // Include the footer
    include_once 'components/footer.php';
    ?>

    <div id="productModal" class="fixed inset-0 modal-overlay hidden z-50">
        <div class="relative bg-white p-8 rounded-lg shadow-xl w-full max-w-lg mx-auto">
            <h3 id="productModalTitle" class="text-2xl font-bold mb-6 text-gray-800"></h3>

            <form action="../../controllers/admin/products.php" method="POST" class="space-y-5">
                <input type="hidden" id="productFormAction" name="action" value="">
                <input type="hidden" id="productId" name="id">

                <div>
                    <label for="formNamaBarang" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang:</label>
                    <input type="text" id="formNamaBarang" name="nama_barang" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-base" required>
                </div>

                <div>
                    <label for="formKategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori:</label>
                    <select id="formKategori" name="id_kategori" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-base" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kategori_options as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['id']); ?>">
                                <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="formPemasok" class="block text-sm font-medium text-gray-700 mb-1">Pemasok:</label>
                    <select id="formPemasok" name="id_pemasok" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-base" required>
                        <option value="">Pilih Pemasok</option>
                        <?php foreach ($pemasok_options as $pem): ?>
                            <option value="<?php echo htmlspecialchars($pem['id']); ?>">
                                <?php echo htmlspecialchars($pem['kontak']); ?> <!-- nyammbung sama pemasok klo ini-->
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="stokFieldWrapper">
                    <label for="formStok" class="block text-sm font-medium text-gray-700 mb-1">Stok:</label>
                    <input type="number" id="formStok" name="stok" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-base" min="0" required>
                </div>

                <div>
                    <label for="formHargaBeli" class="block text-sm font-medium text-gray-700 mb-1">Harga Beli:</label>
                    <input type="number" id="formHargaBeli" name="harga_beli" step="0.01" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-base" min="0" required>
                </div>

                <div>
                    <label for="formHargaJual" class="block text-sm font-medium text-gray-700 mb-1">Harga Jual:</label>
                    <input type="number" id="formHargaJual" name="harga_jual" step="0.01" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-base" min="0" required>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeProductModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-5 rounded-md transition-colors duration-200">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-md shadow-md transition-colors duration-200">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/products.js"></script>
    <script>
        // Flash message dismissal
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                // Optional: Automatically hide after some seconds
                setTimeout(() => {
                    flashMessage.style.display = 'none';
                }, 5000); // Hide after 5 seconds
            }
        });

        // This confirmDelete function is good to have here
        function confirmDelete() {
            return confirm('Apakah Anda yakin ingin menghapus data ini? Aksi ini tidak dapat dibatalkan.');
        }
    </script>
</body>

</html>