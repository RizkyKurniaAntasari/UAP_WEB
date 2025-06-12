<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori - Admin Dashboard</title>
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
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Manajemen Kategori</h1>
        <p class="text-gray-700 mb-8">Kelola kategori untuk pengelompokan barang.</p>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Daftar Kategori</h2>
                <button id="addCategoryBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">Tambah Kategori Baru</button>
            </div>

            <div class="mb-4">
                <input type="text" id="categorySearch" placeholder="Cari kategori..." class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Nama Kategori</th>
                            <th class="py-3 px-6 text-left">Deskripsi</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light" id="categoryTableBody">
                        </tbody>
                </table>
            </div>

            <div class="flex justify-center mt-6 space-x-2" id="paginationContainer">
                </div>

        </div>
    </main>
    <?php include_once 'components/footer.php'?>

    <div id="categoryModal" class="modal hidden">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800 mb-6">Tambah Kategori Baru</h2>
            <form id="categoryForm">
                <input type="hidden" id="categoryId">
                <div class="mb-4">
                    <label for="categoryName" class="block text-gray-700 text-sm font-semibold mb-2">Nama Kategori</label>
                    <input type="text" id="categoryName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-6">
                    <label for="categoryDescription" class="block text-gray-700 text-sm font-semibold mb-2">Deskripsi</label>
                    <textarea id="categoryDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-800 px-5 py-2 rounded-md hover:bg-gray-400 transition duration-300">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition duration-300">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Data kategori dummy (global)
        let allCategoriesData = [
            { id: 1, name: 'Elektronik', description: 'Produk-produk terkait elektronik dan gadget.' },
            { id: 2, name: 'Pakaian', description: 'Semua jenis pakaian dan aksesoris fashion.' },
            { id: 3, name: 'Makanan', description: 'Produk makanan jadi, bahan makanan, dan olahan.' },
            { id: 4, name: 'Minuman', description: 'Berbagai jenis minuman, baik kemasan maupun segar.' },
            { id: 5, name: 'Peralatan Rumah Tangga', description: 'Perlengkapan untuk kebutuhan rumah tangga.' },
            { id: 6, name: 'Otomotif', description: 'Produk dan aksesoris kendaraan.' },
            { id: 7, name: 'Kesehatan', description: 'Produk kesehatan dan kecantikan.' }
        ];

        let filteredCategories = []; // Data setelah disaring
        let currentPage = 1;
        const itemsPerPage = 5; // Jumlah item per halaman

        // --- DOM Elements ---
        const categoryTableBody = document.getElementById('categoryTableBody');
        const categorySearchInput = document.getElementById('categorySearch');
        const paginationContainer = document.getElementById('paginationContainer');
        const addCategoryBtn = document.getElementById('addCategoryBtn');

        // Modal Elements
        const categoryModal = document.getElementById('categoryModal');
        const modalTitle = document.getElementById('modalTitle');
        const categoryForm = document.getElementById('categoryForm');
        const categoryIdInput = document.getElementById('categoryId');
        const categoryNameInput = document.getElementById('categoryName');
        const categoryDescriptionInput = document.getElementById('categoryDescription');

        // --- Fungsi Helper ---

        // Fungsi untuk merender kategori ke tabel
        function renderCategories() {
            categoryTableBody.innerHTML = ''; // Bersihkan tabel sebelum render ulang

            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedCategories = filteredCategories.slice(start, end);

            if (paginatedCategories.length === 0) {
                categoryTableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="py-4 px-6 text-center text-gray-500">Tidak ada kategori yang ditemukan.</td>
                    </tr>
                `;
                renderPagination();
                return;
            }

            paginatedCategories.forEach(category => {
                const row = `
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">${category.id}</td>
                        <td class="py-3 px-6 text-left">${category.name}</td>
                        <td class="py-3 px-6 text-left">${category.description}</td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <button onclick="openModal(${category.id})" class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Edit">
                                    ✏️
                                </button>
                                <button onclick="deleteCategory(${category.id})" class="w-6 h-6 transform hover:text-red-500 hover:scale-110" title="Hapus">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                categoryTableBody.innerHTML += row;
            });
            renderPagination();
        }

        // Fungsi untuk merender kontrol paginasi
        function renderPagination() {
            paginationContainer.innerHTML = '';
            const totalPages = Math.ceil(filteredCategories.length / itemsPerPage);

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
                    renderCategories();
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

        // --- Fungsionalitas Pencarian (otomatis) ---
        function applySearch() {
            const searchTerm = categorySearchInput.value.toLowerCase().trim();

            filteredCategories = allCategoriesData.filter(category =>
                category.name.toLowerCase().includes(searchTerm) ||
                category.description.toLowerCase().includes(searchTerm) ||
                category.id.toString().includes(searchTerm) // Cari juga berdasarkan ID
            );

            currentPage = 1; // Reset ke halaman pertama setelah pencarian
            renderCategories();
        }

        // --- Fungsionalitas CRUD (Tambah, Edit, Hapus) ---

        // Fungsi untuk membuka modal (Tambah atau Edit)
        window.openModal = function(categoryIdParam = null) { // Dibuat global untuk onclick HTML
            categoryForm.reset(); // Bersihkan form
            categoryIdInput.value = ''; // Reset ID kategori

            if (categoryIdParam) {
                modalTitle.textContent = 'Edit Kategori';
                const category = allCategoriesData.find(c => c.id === categoryIdParam);
                if (category) {
                    categoryIdInput.value = category.id;
                    categoryNameInput.value = category.name;
                    categoryDescriptionInput.value = category.description;
                }
            } else {
                modalTitle.textContent = 'Tambah Kategori Baru';
            }
            categoryModal.classList.remove('hidden'); // Tampilkan modal
        }

        // Fungsi untuk menutup modal
        window.closeModal = function() { // Dibuat global untuk onclick HTML
            categoryModal.classList.add('hidden'); // Sembunyikan modal
        }

        // Fungsi untuk menghapus kategori
        window.deleteCategory = function(id) { // Dibuat global untuk onclick HTML
            if (confirm(`Anda yakin ingin menghapus kategori dengan ID ${id}?`)) {
                allCategoriesData = allCategoriesData.filter(category => category.id !== id);
                applySearch(); // Render ulang tabel setelah penghapusan
                alert('Kategori berhasil dihapus.');
            }
        }

        // Handle submit form kategori (Tambah atau Edit)
        categoryForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const id = categoryIdInput.value ? parseInt(categoryIdInput.value) : null;
            const name = categoryNameInput.value.trim();
            const description = categoryDescriptionInput.value.trim();

            // Basic validation
            if (!name) {
                alert('Nama kategori wajib diisi.');
                return;
            }
            // Optional: cek nama kategori duplikat
            if (allCategoriesData.some(cat => cat.name.toLowerCase() === name.toLowerCase() && cat.id !== id)) {
                alert('Nama kategori sudah ada.');
                return;
            }


            if (id) {
                // Mode Edit
                const index = allCategoriesData.findIndex(c => c.id === id);
                if (index !== -1) {
                    allCategoriesData[index] = { id, name, description };
                    alert(`Kategori '${name}' berhasil diperbarui.`);
                }
            } else {
                // Mode Tambah
                const newId = allCategoriesData.length > 0 ? Math.max(...allCategoriesData.map(c => c.id)) + 1 : 1;
                const newCategory = { id: newId, name, description };
                allCategoriesData.push(newCategory);
                alert(`Kategori '${name}' berhasil ditambahkan.`);
            }

            applySearch(); // Perbarui tampilan
            closeModal(); // Tutup modal
        });


        // --- Event Listeners Awal ---
        document.addEventListener('DOMContentLoaded', function() {
            // Autentikasi
            if (localStorage.getItem('userRole') !== 'admin') {
                window.location.href = '../../index.php'; // Kembali ke index.php di root
                return;
            }

            // Initial render
            applySearch(); // Akan memanggil renderCategories() dan renderPagination()

            // Event listener untuk tombol Tambah Kategori Baru
            addCategoryBtn.addEventListener('click', () => openModal(null));

            // Event listener untuk input pencarian (langsung filter saat mengetik)
            categorySearchInput.addEventListener('keyup', applySearch);
        });

        // Fungsi logout client-side (dari components/navbar.php atau di sini jika tidak termasuk)
        function logoutClientSide(event) {
            event.preventDefault();
            localStorage.removeItem('userRole');
            localStorage.removeItem('userEmail');
            window.location.href = '../../logout.php';
        }
    </script>
</body>
</html>