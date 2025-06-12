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

    <?php include_once 'components/navbar.php'?>

    <main class="container mx-auto px-6 py-8 flex-grow">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Manajemen Barang</h1>
        <p class="text-gray-700 mb-8">Kelola data barang, stok, kategori, dan pemasok.</p>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Daftar Barang</h2>
                <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">Tambah Barang Baru</a>
            </div>

            <div class="mb-4 flex flex-col md:flex-row items-center space-y-3 md:space-y-0 md:space-x-4">
                <input type="text" id="productSearch" placeholder="Cari nama barang..." class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">
                <select id="categoryFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/4">
                    <option value="">Semua Kategori</option>
                    <option value="Elektronik">Elektronik</option>
                    <option value="Pakaian">Pakaian</option>
                    <option value="Makanan">Makanan</option>
                    <option value="Minuman">Minuman</option>
                </select>
                <select id="supplierFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/4">
                    <option value="">Semua Pemasok</option>
                    <option value="PT. ABC Global">PT. ABC Global</option>
                    <option value="CV. Jaya Mandiri">CV. Jaya Mandiri</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Nama Barang</th>
                            <th class="py-3 px-6 text-left">Kategori</th>
                            <th class="py-3 px-6 text-left">Pemasok</th>
                            <th class="py-3 px-6 text-center">Stok</th>
                            <th class="py-3 px-6 text-right">Harga Satuan</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light" id="productTableBody">
                        </tbody>
                </table>
            </div>

            <div class="flex justify-center mt-6 space-x-2">
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">Previous</a>
                <a href="#" class="px-4 py-2 border border-blue-500 bg-blue-500 text-white rounded-md">1</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">2</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">Next</a>
            </div>

        </div>
    </main>

    <?php include_once 'components/footer.php'?>

    <div id="editProductModal" class="modal hidden">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Barang</h2>
            <form id="editProductForm">
                <input type="hidden" id="editProductId">
                <div class="mb-4">
                    <label for="editProductName" class="block text-gray-700 text-sm font-semibold mb-2">Nama Barang</label>
                    <input type="text" id="editProductName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="editProductCategory" class="block text-gray-700 text-sm font-semibold mb-2">Kategori</label>
                    <select id="editProductCategory" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="Elektronik">Elektronik</option>
                        <option value="Pakaian">Pakaian</option>
                        <option value="Makanan">Makanan</option>
                        <option value="Minuman">Minuman</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="editProductSupplier" class="block text-gray-700 text-sm font-semibold mb-2">Pemasok</label>
                    <select id="editProductSupplier" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="PT. ABC Global">PT. ABC Global</option>
                        <option value="CV. Jaya Mandiri">CV. Jaya Mandiri</option>
                        <option value="UMKM Donut Sejahtera">UMKM Donut Sejahtera</option>
                        <option value="PT. Tirta Segar">PT. Tirta Segar</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="editProductStock" class="block text-gray-700 text-sm font-semibold mb-2">Stok</label>
                    <input type="number" id="editProductStock" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required min="0">
                </div>
                <div class="mb-6">
                    <label for="editProductPrice" class="block text-gray-700 text-sm font-semibold mb-2">Harga Satuan</label>
                    <input type="number" id="editProductPrice" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required min="0">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-800 px-5 py-2 rounded-md hover:bg-gray-400 transition duration-300">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition duration-300">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Data produk dummy (global agar bisa diakses fungsi lain)
        let productsData = [
            { id: 1, name: 'Laptop Gaming X1', category: 'Elektronik', supplier: 'PT. ABC Global', stock: 15, price: 15000000 },
            { id: 2, name: 'Kemeja Pria Casual', category: 'Pakaian', supplier: 'CV. Jaya Mandiri', stock: 200, price: 120000 },
            { id: 3, name: 'Berliner Coklat', category: 'Makanan', supplier: 'UMKM Donut Sejahtera', stock: 50, price: 15000 },
            { id: 4, name: 'Mouse Wireless A10', category: 'Elektronik', supplier: 'PT. ABC Global', stock: 75, price: 180000 },
            { id: 5, name: 'Celana Jeans Slim Fit', category: 'Pakaian', supplier: 'CV. Jaya Mandiri', stock: 150, price: 250000 },
            { id: 6, name: 'Air Mineral 600ml', category: 'Minuman', supplier: 'PT. Tirta Segar', stock: 300, price: 3000 },
        ];

        const productTableBody = document.getElementById('productTableBody');
        const editProductModal = document.getElementById('editProductModal');
        const editProductForm = document.getElementById('editProductForm');
        const editProductId = document.getElementById('editProductId');
        const editProductName = document.getElementById('editProductName');
        const editProductCategory = document.getElementById('editProductCategory');
        const editProductSupplier = document.getElementById('editProductSupplier');
        const editProductStock = document.getElementById('editProductStock');
        const editProductPrice = document.getElementById('editProductPrice');


        // Fungsi untuk menampilkan data barang ke tabel
        function renderProducts() {
            productTableBody.innerHTML = ''; // Bersihkan tabel sebelum render ulang
            productsData.forEach(product => {
                const row = `
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">${product.id}</td>
                        <td class="py-3 px-6 text-left">${product.name}</td>
                        <td class="py-3 px-6 text-left">${product.category}</td>
                        <td class="py-3 px-6 text-left">${product.supplier}</td>
                        <td class="py-3 px-6 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                ${product.stock < 20 ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800'}
                            ">${product.stock}</span>
                        </td>
                        <td class="py-3 px-6 text-right">Rp ${product.price.toLocaleString('id-ID')}</td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <button class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Edit" onclick="openEditModal(${product.id})">
                                    ✏️
                                </button>
                                <button class="w-6 h-6 transform hover:text-red-500 hover:scale-110" title="Hapus" onclick="deleteProduct(${product.id})">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                productTableBody.innerHTML += row;
            });
        }

        // --- Fungsionalitas Hapus (Delete) ---
        function deleteProduct(id) {
            if (confirm(`Apakah Anda yakin ingin menghapus barang dengan ID ${id}?`)) {
                productsData = productsData.filter(product => product.id !== id);
                renderProducts(); // Render ulang tabel setelah penghapusan
            }
        }

        // --- Fungsionalitas Edit (Update) ---
        function openEditModal(id) {
            const productToEdit = productsData.find(product => product.id === id);
            if (productToEdit) {
                editProductId.value = productToEdit.id;
                editProductName.value = productToEdit.name;
                editProductCategory.value = productToEdit.category;
                editProductSupplier.value = productToEdit.supplier;
                editProductStock.value = productToEdit.stock;
                editProductPrice.value = productToEdit.price;
                editProductModal.style.display = 'flex'; // Tampilkan modal
            }
        }

        function closeModal() {
            editProductModal.style.display = 'none'; // Sembunyikan modal
        }

        editProductForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form submit default

            const id = parseInt(editProductId.value);
            const newName = editProductName.value;
            const newCategory = editProductCategory.value;
            const newSupplier = editProductSupplier.value;
            const newStock = parseInt(editProductStock.value);
            const newPrice = parseInt(editProductPrice.value);

            // Temukan dan perbarui data produk
            const productIndex = productsData.findIndex(product => product.id === id);
            if (productIndex !== -1) {
                productsData[productIndex] = {
                    id: id,
                    name: newName,
                    category: newCategory,
                    supplier: newSupplier,
                    stock: newStock,
                    price: newPrice
                };
                renderProducts(); // Render ulang tabel setelah pembaruan
                closeModal(); // Tutup modal
            }
        });

        // --- Logika Autentikasi dan Render Awal ---
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('userRole') !== 'admin') {
                window.location.href = '../../index.php'; // Kembali ke index.php di root
            }
            renderProducts(); // Panggil fungsi untuk menampilkan data saat halaman dimuat
        });

        // Fungsi logout client-side (tetap sama)
        function logoutClientSide(event) {
            event.preventDefault();
            localStorage.removeItem('userRole');
            localStorage.removeItem('userEmail');
            window.location.href = '../../logout.php'; // Path ke logout.php di root
        }
    </script>

    
</body>
</html>