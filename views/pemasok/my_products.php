<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Saya - Pemasok Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans flex flex-col min-h-screen">
    <nav class="bg-green-700 p-4 shadow-md text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="dashboard.php" class="text-2xl font-bold">Pemasok Dashboard</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:text-green-200">Beranda</a>
                <a href="my_products.php" class="hover:text-green-200 font-semibold">Produk Saya</a>
                <a href="orders.php" class="hover:text-green-200">Pesanan</a>
                <a href="../../logout.php" class="bg-red-600 px-3 py-1 rounded-md hover:bg-red-700 transition duration-300" onclick="logoutClientSide(event)">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-8 flex-grow">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Produk yang Saya Sediakan</h1>
        <p class="text-gray-700 mb-8">Daftar barang yang Anda sediakan untuk sistem inventaris.</p>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Daftar Produk</h2>
                <button id="addProductBtn" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-300">Ajukan Produk Baru</button>
            </div>

            <div class="mb-4">
                <input type="text" id="searchInput" placeholder="Cari nama produk..." class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 w-full md:w-1/3">
            </div>

            <div id="flashMessage" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span id="flashMessageText" class="block sm:inline"></span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.classList.add('hidden')">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.697l-2.651 2.652a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.651 7.348a1.2 1.2 0 1 1 1.697-1.697L10 8.303l2.651-2.652a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>


            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Nama Barang</th>
                            <th class="py-3 px-6 text-left">Kategori</th>
                            <th class="py-3 px-6 text-center">Stok Tersedia</th>
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

    <footer class="bg-gray-800 text-white py-4 text-center mt-8">
        <div class="container mx-auto px-6">
            <p class="text-sm">&copy; 2025 Sistem Inventory. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <div id="productModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
            <h3 id="modalTitle" class="text-2xl font-bold mb-4">Ajukan Produk Baru</h3>
            <form id="productForm">
                <input type="hidden" id="productId" name="id">
                <div class="mb-4">
                    <label for="productName" class="block text-gray-700 text-sm font-bold mb-2">Nama Produk:</label>
                    <input type="text" id="productName" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="productCategory" class="block text-gray-700 text-sm font-bold mb-2">Kategori:</label>
                    <select id="productCategory" name="category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Elektronik">Elektronik</option>
                        <option value="Pakaian">Pakaian</option>
                        <option value="Makanan">Makanan</option>
                        <option value="Minuman">Minuman</option>
                        <option value="Perlengkapan Rumah">Perlengkapan Rumah</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="productStock" class="block text-gray-700 text-sm font-bold mb-2">Stok Tersedia:</label>
                    <input type="number" id="productStock" name="stock" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0">
                </div>
                <div class="mb-4">
                    <label for="productPrice" class="block text-gray-700 text-sm font-bold mb-2">Harga Satuan (Rp):</label>
                    <input type="number" id="productPrice" name="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required step="0.01" min="0">
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancelModalBtn" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">Batal</button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Fungsi logout client-side (tetap sama)
        function logoutClientSide(event) {
            event.preventDefault();
            localStorage.removeItem('userRole');
            localStorage.removeItem('userEmail');
            window.location.href = '../../logout.php';
        }

        // --- Variabel Global untuk Data Produk dan Paginasi ---
        let allProductsData = []; // Akan diisi saat DOMContentLoaded
        let filteredProducts = [];
        let currentPage = 1;
        const itemsPerPage = 5; // Sesuaikan jumlah produk per halaman
        const currentUserEmail = localStorage.getItem('userEmail');

        // --- DOM Elements ---
        const productTableBody = document.getElementById('productTableBody');
        const searchInput = document.getElementById('searchInput');
        const paginationContainer = document.getElementById('paginationContainer');
        const addProductBtn = document.getElementById('addProductBtn');
        const productModal = document.getElementById('productModal');
        const modalTitle = document.getElementById('modalTitle');
        const productForm = document.getElementById('productForm');
        const productIdInput = document.getElementById('productId');
        const productNameInput = document.getElementById('productName');
        const productCategoryInput = document.getElementById('productCategory');
        const productStockInput = document.getElementById('productStock');
        const productPriceInput = document.getElementById('productPrice');
        const cancelModalBtn = document.getElementById('cancelModalBtn');
        const flashMessage = document.getElementById('flashMessage');
        const flashMessageText = document.getElementById('flashMessageText');


        // --- Fungsi Helper ---

        // Fungsi untuk menampilkan pesan flash
        function showFlashMessage(message, type = 'success') {
            flashMessageText.textContent = message;
            flashMessage.classList.remove('hidden', 'bg-green-100', 'bg-red-100', 'border-green-400', 'border-red-400', 'text-green-700', 'text-red-700');
            if (type === 'success') {
                flashMessage.classList.add('bg-green-100', 'border-green-400', 'text-green-700');
            } else {
                flashMessage.classList.add('bg-red-100', 'border-red-400', 'text-red-700');
            }
            setTimeout(() => {
                flashMessage.classList.add('hidden');
            }, 5000); // Pesan akan hilang setelah 5 detik
        }

        // Fungsi untuk memformat harga ke Rupiah
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Fungsi untuk merender produk ke tabel
        function renderProducts() {
            productTableBody.innerHTML = ''; // Bersihkan tabel
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedProducts = filteredProducts.slice(start, end);

            if (paginatedProducts.length === 0) {
                productTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="py-4 px-6 text-center text-gray-500">
                            ${searchInput.value.trim() !== '' ? 'Produk tidak ditemukan.' : 'Belum ada produk yang Anda sediakan.'}
                        </td>
                    </tr>
                `;
                renderPagination(); // Render paginasi meskipun tidak ada data
                return;
            }

            paginatedProducts.forEach(product => {
                const row = `
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">${product.id}</td>
                        <td class="py-3 px-6 text-left">${product.name}</td>
                        <td class="py-3 px-6 text-left">${product.category}</td>
                        <td class="py-3 px-6 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                ${product.stock < 20 ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800'}
                            ">${product.stock}</span>
                        </td>
                        <td class="py-3 px-6 text-right">${formatRupiah(product.price)}</td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <button onclick="editProduct(${product.id})" class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Edit">
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

            if (totalPages <= 1 && searchInput.value.trim() === '') {
                return; // Jangan tampilkan paginasi jika hanya ada 1 halaman dan tidak sedang mencari
            }

            const createPageLink = (page, text, isActive = false) => {
                const link = document.createElement('a');
                link.href = '#';
                link.className = `px-4 py-2 border rounded-md hover:bg-gray-200 ${isActive ? 'border-green-500 bg-green-500 text-white' : 'border-gray-300'}`;
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

        // Fungsi untuk menyaring produk berdasarkan input pencarian
        function filterProducts() {
            const searchTerm = searchInput.value.toLowerCase();
            filteredProducts = allProductsData.filter(product =>
                product.name.toLowerCase().includes(searchTerm)
            );
            currentPage = 1; // Reset ke halaman pertama setelah pencarian
            renderProducts();
        }

        // --- Fungsi CRUD (Add, Edit, Delete) ---

        // Fungsi untuk membuka modal tambah produk
        addProductBtn.addEventListener('click', () => {
            modalTitle.textContent = 'Ajukan Produk Baru';
            productForm.reset(); // Bersihkan form
            productIdInput.value = ''; // Pastikan ID kosong untuk mode tambah
            productModal.classList.remove('hidden');
        });

        // Fungsi untuk membuka modal edit produk
        function editProduct(id) {
            const product = allProductsData.find(p => p.id === id);
            if (product) {
                modalTitle.textContent = 'Edit Produk';
                productIdInput.value = product.id;
                productNameInput.value = product.name;
                productCategoryInput.value = product.category;
                productStockInput.value = product.stock;
                productPriceInput.value = product.price;
                productModal.classList.remove('hidden');
            }
        }

        // Fungsi untuk menangani submit form (tambah/edit)
        productForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const id = productIdInput.value ? parseInt(productIdInput.value) : null;
            const name = productNameInput.value.trim();
            const category = productCategoryInput.value;
            const stock = parseInt(productStockInput.value);
            const price = parseFloat(productPriceInput.value);

            if (!name || !category || isNaN(stock) || isNaN(price)) {
                showFlashMessage("Semua field wajib diisi dengan format yang benar.", "error");
                return;
            }
            if (stock < 0 || price < 0) {
                 showFlashMessage("Stok dan harga tidak boleh negatif.", "error");
                 return;
            }

            if (id) {
                // Mode Edit
                const index = allProductsData.findIndex(p => p.id === id);
                if (index !== -1) {
                    allProductsData[index] = { ...allProductsData[index], name, category, stock, price };
                    showFlashMessage(`Produk '${name}' berhasil diperbarui.`, "success");
                }
            } else {
                // Mode Tambah
                const newId = allProductsData.length > 0 ? Math.max(...allProductsData.map(p => p.id)) + 1 : 1;
                const newProduct = {
                    id: newId,
                    name,
                    category,
                    supplierEmail: currentUserEmail, // Assign ke pemasok yang sedang login
                    stock,
                    price
                };
                allProductsData.push(newProduct);
                showFlashMessage(`Produk '${name}' berhasil ditambahkan.`, "success");
            }

            // Perbarui data yang difilter dan render ulang
            filterProducts();
            productModal.classList.add('hidden'); // Tutup modal
        });

        // Fungsi untuk menutup modal
        cancelModalBtn.addEventListener('click', () => {
            productModal.classList.add('hidden');
            productForm.reset();
        });

        // Fungsi untuk menghapus produk
        function deleteProduct(id) {
            if (confirm('Anda yakin ingin menghapus produk ini?')) {
                const initialLength = allProductsData.length;
                allProductsData = allProductsData.filter(product => product.id !== id);

                if (allProductsData.length < initialLength) {
                    showFlashMessage("Produk berhasil dihapus.", "success");
                } else {
                    showFlashMessage("Gagal menghapus produk.", "error");
                }
                filterProducts(); // Perbarui tampilan setelah penghapusan
            }
        }

        // --- Event Listeners ---
        document.addEventListener('DOMContentLoaded', function() {
            // Logika autentikasi sisi klien (tetap sama)
            if (localStorage.getItem('userRole') !== 'pemasok') {
                window.location.href = '../../index.php'; // Kembali ke index.php di root
            }

            // Data produk dummy (hanya produk yang diasosiasikan dengan 'pemasok@example.com')
            const initialAllProductsData = [
                { id: 1, name: 'Laptop Gaming X1', category: 'Elektronik', supplierEmail: 'ptabclobal@example.com', stock: 15, price: 15000000 },
                { id: 2, name: 'Kemeja Pria Casual', category: 'Pakaian', supplierEmail: 'pemasok@example.com', stock: 200, price: 120000 },
                { id: 3, name: 'Berliner Coklat', category: 'Makanan', supplierEmail: 'umkmdonut@example.com', stock: 50, price: 15000 },
                { id: 4, name: 'Mouse Wireless A10', category: 'Elektronik', supplierEmail: 'ptabclobal@example.com', stock: 75, price: 180000 },
                { id: 5, name: 'Celana Jeans Slim Fit', category: 'Pakaian', supplierEmail: 'pemasok@example.com', stock: 150, price: 250000 },
                { id: 6, name: 'Air Mineral 600ml', category: 'Minuman', supplierEmail: 'pttirta@example.com', stock: 300, price: 3000 },
                { id: 7, name: 'Kaos Oblong Unisex', category: 'Pakaian', supplierEmail: 'pemasok@example.com', stock: 100, price: 75000 },
                { id: 8, name: 'Charger USB-C', category: 'Elektronik', supplierEmail: 'ptabclobal@example.com', stock: 25, price: 90000 },
                { id: 9, name: 'Buku Resep Masakan', category: 'Peralatan Dapur', supplierEmail: 'umkmdonut@example.com', stock: 10, price: 50000 },
                { id: 10, name: 'Sepatu Lari Sport', category: 'Pakaian', supplierEmail: 'pemasok@example.com', stock: 40, price: 450000 },
                { id: 11, name: 'Kopi Bubuk Robusta', category: 'Minuman', supplierEmail: 'pttirta@example.com', stock: 60, price: 25000 },
            ];

            // Filter data dummy hanya untuk pemasok yang sedang login
            allProductsData = initialAllProductsData.filter(product => product.supplierEmail === currentUserEmail);

            // Inisialisasi tampilan
            filterProducts(); // Akan memanggil renderProducts() dan renderPagination()
        });

        searchInput.addEventListener('keyup', filterProducts);
        // Tambahkan event listener untuk tombol paginasi jika Anda ingin mengimplementasikan dengan tombol terpisah,
        // namun fungsi renderPagination() sudah menangani click event pada link yang dibuatnya.
    </script>
</body>
</html>