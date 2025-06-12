<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Gaya tambahan untuk modal */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px; /* Sesuaikan lebar modal */
            position: relative;
        }
        .close-button {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6B7280; /* gray-500 */
        }
    </style>
</head>
<body class="bg-gray-100 font-sans flex flex-col min-h-screen">

    <?php include_once 'components/navbar.php'?>

    <main class="container mx-auto px-6 py-8 flex-grow">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Manajemen Barang</h1>
        <p class="text-gray-700 mb-8">Kelola data barang, stok, kategori, dan pemasok.</p>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Daftar Barang</h2>
                <button id="addOrEditProductBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">Tambah Barang Baru</button>
            </div>

            <div class="mb-4 flex flex-col md:flex-row items-center space-y-3 md:space-y-0 md:space-x-4">
                <input type="text" id="productSearch" placeholder="Cari nama barang..." class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">
                <select id="categoryFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/4">
                    <option value="">Semua Kategori</option>
                    </select>
                <select id="supplierFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/4">
                    <option value="">Semua Pemasok</option>
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

            <div class="flex justify-center mt-6 space-x-2" id="paginationContainer">
                </div>

        </div>
    </main>

    <?php include_once 'components/footer.php'?>

    <div id="productModal" class="modal hidden">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800 mb-6">Tambah Barang Baru</h2>
            <form id="productForm">
                <input type="hidden" id="productId">
                <div class="mb-4">
                    <label for="productName" class="block text-gray-700 text-sm font-semibold mb-2">Nama Barang</label>
                    <input type="text" id="productName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="productCategory" class="block text-gray-700 text-sm font-semibold mb-2">Kategori</label>
                    <select id="productCategory" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </select>
                </div>
                <div class="mb-4">
                    <label for="productSupplier" class="block text-gray-700 text-sm font-semibold mb-2">Pemasok</label>
                    <select id="productSupplier" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </select>
                </div>
                <div class="mb-4">
                    <label for="productStock" class="block text-gray-700 text-sm font-semibold mb-2">Stok</label>
                    <input type="number" id="productStock" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required min="0">
                </div>
                <div class="mb-6">
                    <label for="productPrice" class="block text-gray-700 text-sm font-semibold mb-2">Harga Satuan</label>
                    <input type="number" id="productPrice" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required min="0">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-800 px-5 py-2 rounded-md hover:bg-gray-400 transition duration-300">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition duration-300">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // --- Data Dummy (Global) ---
        let allProductsData = [
            { id: 1, name: 'Laptop Gaming X1', category: 'Elektronik', supplier: 'PT. ABC Global', stock: 15, price: 15000000 },
            { id: 2, name: 'Kemeja Pria Casual', category: 'Pakaian', supplier: 'CV. Jaya Mandiri', stock: 200, price: 120000 },
            { id: 3, name: 'Berliner Coklat', category: 'Makanan', supplier: 'UMKM Donut Sejahtera', stock: 50, price: 15000 },
            { id: 4, name: 'Mouse Wireless A10', category: 'Elektronik', supplier: 'PT. ABC Global', stock: 75, price: 180000 },
            { id: 5, name: 'Celana Jeans Slim Fit', category: 'Pakaian', supplier: 'CV. Jaya Mandiri', stock: 150, price: 250000 },
            { id: 6, name: 'Air Mineral 600ml', category: 'Minuman', supplier: 'PT. Tirta Segar', stock: 300, price: 3000 },
            { id: 7, name: 'Kaos Oblong Unisex', category: 'Pakaian', supplier: 'CV. Jaya Mandiri', stock: 100, price: 75000 },
            { id: 8, name: 'Charger USB-C', category: 'Elektronik', supplier: 'PT. ABC Global', stock: 25, price: 90000 },
            { id: 9, name: 'Buku Resep Masakan', category: 'Makanan', supplier: 'UMKM Donut Sejahtera', stock: 10, price: 50000 },
            { id: 10, name: 'Sepatu Lari Sport', category: 'Pakaian', supplier: 'CV. Jaya Mandiri', stock: 40, price: 450000 },
            { id: 11, name: 'Kopi Bubuk Robusta', category: 'Minuman', supplier: 'PT. Tirta Segar', stock: 60, price: 25000 },
        ];

        let filteredProducts = []; // Data setelah disaring
        let currentPage = 1;
        const itemsPerPage = 5; // Jumlah item per halaman

        // --- DOM Elements ---
        const productTableBody = document.getElementById('productTableBody');
        const productSearchInput = document.getElementById('productSearch');
        const categoryFilterSelect = document.getElementById('categoryFilter');
        const supplierFilterSelect = document.getElementById('supplierFilter');
        // const applyFilterBtn = document.getElementById('applyFilterBtn'); // Dihapus
        // const resetFilterBtn = document.getElementById('resetFilterBtn'); // Dihapus
        const paginationContainer = document.getElementById('paginationContainer');
        const addOrEditProductBtn = document.getElementById('addOrEditProductBtn');

        // Modal Elements
        const productModal = document.getElementById('productModal');
        const modalTitle = document.getElementById('modalTitle');
        const productForm = document.getElementById('productForm');
        const productIdInput = document.getElementById('productId');
        const productNameInput = document.getElementById('productName');
        const productCategoryInput = document.getElementById('productCategory');
        const productSupplierInput = document.getElementById('productSupplier');
        const productStockInput = document.getElementById('productStock');
        const productPriceInput = document.getElementById('productPrice');

        // --- Fungsi Helper ---

        // Fungsi untuk memformat harga ke Rupiah
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Fungsi untuk mengisi dropdown filter (kategori dan pemasok)
        function populateFilters() {
            // Hapus semua opsi kecuali yang pertama (Semua Kategori/Pemasok)
            while (categoryFilterSelect.options.length > 1) {
                categoryFilterSelect.remove(1);
            }
            while (supplierFilterSelect.options.length > 1) {
                supplierFilterSelect.remove(1);
            }
            // Hapus semua opsi dari modal selects sebelum mengisi ulang
            productCategoryInput.innerHTML = '';
            productSupplierInput.innerHTML = '';
            // Tambahkan opsi default "Pilih Kategori/Pemasok" untuk modal
            productCategoryInput.add(new Option("Pilih Kategori", ""));
            productSupplierInput.add(new Option("Pilih Pemasok", ""));


            const categories = [...new Set(allProductsData.map(p => p.category))].sort();
            const suppliers = [...new Set(allProductsData.map(p => p.supplier))].sort();

            categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat;
                option.textContent = cat;
                categoryFilterSelect.appendChild(option);
                // Tambahkan juga ke modal select
                const modalOption = option.cloneNode(true);
                productCategoryInput.appendChild(modalOption);
            });

            suppliers.forEach(sup => {
                const option = document.createElement('option');
                option.value = sup;
                option.textContent = sup;
                supplierFilterSelect.appendChild(option);
                // Tambahkan juga ke modal select
                const modalOption = option.cloneNode(true);
                productSupplierInput.appendChild(modalOption);
            });
        }


        // Fungsi untuk merender produk ke tabel
        function renderProducts() {
            productTableBody.innerHTML = ''; // Bersihkan tabel sebelum render ulang

            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedProducts = filteredProducts.slice(start, end);

            if (paginatedProducts.length === 0) {
                productTableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="py-4 px-6 text-center text-gray-500">Tidak ada barang yang ditemukan.</td>
                    </tr>
                `;
                renderPagination();
                return;
            }

            paginatedProducts.forEach(product => {
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
                        <td class="py-3 px-6 text-right">${formatRupiah(product.price)}</td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <button onclick="openModal(${product.id})" class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Edit">
                                    ✏️
                                </button>
                                <button onclick="deleteProduct(${product.id})" class="w-6 h-6 transform hover:text-red-500 hover:scale-110" title="Hapus">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                productTableBody.innerHTML += row;
            });
            renderPagination();
        }

        // Fungsi untuk merender kontrol paginasi
        function renderPagination() {
            paginationContainer.innerHTML = '';
            const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);

            if (totalPages <= 1) { // Hanya tampilkan paginasi jika ada lebih dari 1 halaman
                return;
            }

            const createPageLink = (page, text, isActive = false) => {
                const link = document.createElement('a');
                link.href = '#';
                link.className = `px-4 py-2 border rounded-md hover:bg-gray-200 ${isActive ? 'border-blue-500 bg-blue-500 text-white' : 'border-gray-300'}`;
                link.textContent = text;
                link.onclick = (e) => {
                    e.preventDefault();
                    currentPage = page;
                    renderProducts();
                };
                return link;
            };

            // Previous button
            if (currentPage > 1) {
                paginationContainer.appendChild(createPageLink(currentPage - 1, 'Previous'));
            }

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                paginationContainer.appendChild(createPageLink(i, i.toString(), i === currentPage));
            }

            // Next button
            if (currentPage < totalPages) {
                paginationContainer.appendChild(createPageLink(currentPage + 1, 'Next'));
            }
        }

        // --- Fungsionalitas Filter dan Pencarian (otomatis) ---
        function applyFilters() {
            const searchTerm = productSearchInput.value.toLowerCase().trim();
            const categoryFilter = categoryFilterSelect.value;
            const supplierFilter = supplierFilterSelect.value;

            filteredProducts = allProductsData.filter(product => {
                const matchesSearch = product.name.toLowerCase().includes(searchTerm);
                const matchesCategory = categoryFilter === '' || product.category === categoryFilter;
                const matchesSupplier = supplierFilter === '' || product.supplier === supplierFilter;
                return matchesSearch && matchesCategory && matchesSupplier;
            });

            currentPage = 1; // Reset ke halaman pertama setelah filter/pencarian
            renderProducts();
        }

        // --- Fungsionalitas CRUD (Tambah, Edit, Hapus) ---

        // Fungsi untuk membuka modal (Tambah atau Edit)
        window.openModal = function(productId = null) { // Dibuat global
            productForm.reset(); // Bersihkan form
            productIdInput.value = ''; // Reset ID produk

            // Isi ulang dropdown kategori dan pemasok di modal setiap kali modal dibuka
            // Ini penting karena data allProductsData mungkin berubah setelah tambah/hapus
            // dan kita ingin dropdown modal selalu mencerminkan opsi yang tersedia saat ini.
            productCategoryInput.innerHTML = '';
            productSupplierInput.innerHTML = '';
            productCategoryInput.add(new Option("Pilih Kategori", ""));
            productSupplierInput.add(new Option("Pilih Pemasok", ""));

            const uniqueCategories = [...new Set(allProductsData.map(p => p.category))].sort();
            uniqueCategories.forEach(cat => {
                productCategoryInput.add(new Option(cat, cat));
            });

            const uniqueSuppliers = [...new Set(allProductsData.map(p => p.supplier))].sort();
            uniqueSuppliers.forEach(sup => {
                productSupplierInput.add(new Option(sup, sup));
            });


            if (productId) {
                modalTitle.textContent = 'Edit Barang';
                const product = allProductsData.find(p => p.id === productId);
                if (product) {
                    productIdInput.value = product.id;
                    productNameInput.value = product.name;
                    productCategoryInput.value = product.category;
                    productSupplierInput.value = product.supplier;
                    productStockInput.value = product.stock;
                    productPriceInput.value = product.price;
                }
            } else {
                modalTitle.textContent = 'Tambah Barang Baru';
            }
            productModal.classList.remove('hidden'); // Tampilkan modal
        }

        // Fungsi untuk menutup modal
        window.closeModal = function() { // Dibuat global untuk onclick HTML
            productModal.classList.add('hidden'); // Sembunyikan modal
        }

        // Fungsi untuk menghapus produk
        window.deleteProduct = function(id) { // Dibuat global untuk onclick HTML
            if (confirm(`Anda yakin ingin menghapus barang dengan ID ${id}?`)) {
                allProductsData = allProductsData.filter(product => product.id !== id);
                populateFilters(); // Perbarui opsi filter setelah hapus
                applyFilters(); // Render ulang tabel setelah penghapusan
                alert('Barang berhasil dihapus.');
            }
        }

        // Handle submit form produk (Tambah atau Edit)
        productForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const id = productIdInput.value ? parseInt(productIdInput.value) : null;
            const name = productNameInput.value.trim();
            const category = productCategoryInput.value;
            const supplier = productSupplierInput.value;
            const stock = parseInt(productStockInput.value);
            const price = parseFloat(productPriceInput.value);

            // Basic validation
            if (!name || !category || !supplier || isNaN(stock) || isNaN(price) || stock < 0 || price < 0) {
                alert('Mohon isi semua field dengan benar.');
                return;
            }

            if (id) {
                // Mode Edit
                const index = allProductsData.findIndex(p => p.id === id);
                if (index !== -1) {
                    allProductsData[index] = { id, name, category, supplier, stock, price };
                    alert(`Barang '${name}' berhasil diperbarui.`);
                }
            } else {
                // Mode Tambah
                const newId = allProductsData.length > 0 ? Math.max(...allProductsData.map(p => p.id)) + 1 : 1;
                const newProduct = { id: newId, name, category, supplier, stock, price };
                allProductsData.push(newProduct);
                alert(`Barang '${name}' berhasil ditambahkan.`);
            }

            populateFilters(); // Perbarui opsi filter setelah menambah/mengedit (jika ada kategori/supplier baru)
            applyFilters(); // Perbarui tampilan
            closeModal(); // Tutup modal
        });


        // --- Event Listeners Awal ---
        document.addEventListener('DOMContentLoaded', function() {
            // Autentikasi
            if (localStorage.getItem('userRole') !== 'admin') {
                window.location.href = '../../index.php'; // Kembali ke index.php di root
                return;
            }

            // Inisialisasi: Isi filter dropdown, lalu tampilkan data
            populateFilters();
            applyFilters(); // Akan memanggil renderProducts() dan renderPagination()

            // Event listener untuk tombol Tambah Barang Baru
            addOrEditProductBtn.addEventListener('click', () => openModal(null));

            // Event listener untuk perubahan pada dropdown filter dan input pencarian
            productSearchInput.addEventListener('keyup', applyFilters); // Langsung filter saat mengetik
            categoryFilterSelect.addEventListener('change', applyFilters); // Langsung filter saat dropdown kategori diubah
            supplierFilterSelect.addEventListener('change', applyFilters); // Langsung filter saat dropdown supplier diubah

            // Hapus event listener untuk tombol "Terapkan Filter" dan "Reset Filter"
            // karena mereka sudah dihapus dari HTML dan fungsionalitasnya digantikan oleh event change/keyup
        });

        // Fungsi logout client-side (dari navbar.php atau di sini jika tidak termasuk)
        function logoutClientSide(event) {
            event.preventDefault();
            localStorage.removeItem('userRole');
            localStorage.removeItem('userEmail');
            window.location.href = '../../logout.php';
        }
    </script>

    
</body>
</html>