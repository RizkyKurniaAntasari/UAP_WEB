<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="bg-gray-100 font-sans flex flex-col min-h-screen">

    <?php include_once 'components/navbar.php'?>

    <main class="container mx-auto px-6 py-8 flex-grow">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Manajemen Kategori</h1>
        <p class="text-gray-700 mb-8">Kelola kategori untuk pengelompokan barang.</p>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Daftar Kategori</h2>
                <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300" onclick="openAddModal()">Tambah Kategori Baru</a>
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

            <div class="flex justify-center mt-6 space-x-2">
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">Previous</a>
                <a href="#" class="px-4 py-2 border border-blue-500 bg-blue-500 text-white rounded-md">1</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">2</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">Next</a>
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
        // Data kategori dummy (global agar bisa diakses fungsi lain)
        let categoriesData = [
            { id: 1, name: 'Elektronik', description: 'Produk-produk terkait elektronik dan gadget.' },
            { id: 2, name: 'Pakaian', description: 'Semua jenis pakaian dan aksesoris fashion.' },
            { id: 3, name: 'Makanan', description: 'Produk makanan jadi, bahan makanan, dan olahan.' },
            { id: 4, name: 'Minuman', description: 'Berbagai jenis minuman, baik kemasan maupun segar.' },
            { id: 5, name: 'Peralatan Rumah Tangga', description: 'Perlengkapan untuk kebutuhan rumah tangga.' }
        ];

        const categoryTableBody = document.getElementById('categoryTableBody');
        const categoryModal = document.getElementById('categoryModal');
        const modalTitle = document.getElementById('modalTitle');
        const categoryForm = document.getElementById('categoryForm');
        const categoryId = document.getElementById('categoryId');
        const categoryName = document.getElementById('categoryName');
        const categoryDescription = document.getElementById('categoryDescription');
        let currentEditingId = null; // Untuk melacak kategori yang sedang diedit

        // Fungsi untuk menampilkan data kategori ke tabel
        function renderCategories() {
            categoryTableBody.innerHTML = ''; // Bersihkan tabel sebelum render ulang
            categoriesData.forEach(category => {
                const row = `
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">${category.id}</td>
                        <td class="py-3 px-6 text-left">${category.name}</td>
                        <td class="py-3 px-6 text-left">${category.description}</td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <button class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Edit" onclick="openEditModal(${category.id})">
                                    ✏️
                                </button>
                                <button class="w-6 h-6 transform hover:text-red-500 hover:scale-110" title="Hapus" onclick="deleteCategory(${category.id})">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                categoryTableBody.innerHTML += row;
            });
        }

        // --- Fungsionalitas Hapus (Delete) ---
        function deleteCategory(id) {
            if (confirm(`Apakah Anda yakin ingin menghapus kategori dengan ID ${id}?`)) {
                categoriesData = categoriesData.filter(category => category.id !== id);
                renderCategories(); // Render ulang tabel setelah penghapusan
            }
        }

        // --- Fungsionalitas Tambah (Create) & Edit (Update) via Modal ---
        function openAddModal() {
            modalTitle.textContent = 'Tambah Kategori Baru';
            categoryForm.reset(); // Kosongkan formulir
            categoryId.value = ''; // Pastikan ID kosong untuk mode tambah
            currentEditingId = null;
            categoryModal.style.display = 'flex'; // Tampilkan modal
        }

        function openEditModal(id) {
            modalTitle.textContent = 'Edit Kategori';
            const categoryToEdit = categoriesData.find(category => category.id === id);
            if (categoryToEdit) {
                categoryId.value = categoryToEdit.id;
                categoryName.value = categoryToEdit.name;
                categoryDescription.value = categoryToEdit.description;
                currentEditingId = id; // Simpan ID kategori yang sedang diedit
                categoryModal.style.display = 'flex'; // Tampilkan modal
            }
        }

        function closeModal() {
            categoryModal.style.display = 'none'; // Sembunyikan modal
        }

        categoryForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form submit default

            const id = categoryId.value ? parseInt(categoryId.value) : null;
            const name = categoryName.value;
            const description = categoryDescription.value;

            if (currentEditingId) {
                // Mode Edit (Update)
                const categoryIndex = categoriesData.findIndex(cat => cat.id === id);
                if (categoryIndex !== -1) {
                    categoriesData[categoryIndex] = {
                        id: id,
                        name: name,
                        description: description
                    };
                }
            } else {
                // Mode Tambah (Create)
                const newId = categoriesData.length > 0 ? Math.max(...categoriesData.map(cat => cat.id)) + 1 : 1;
                categoriesData.push({
                    id: newId,
                    name: name,
                    description: description
                });
            }
            renderCategories(); // Render ulang tabel setelah penambahan/pembaruan
            closeModal(); // Tutup modal
        });


        // --- Logika Autentikasi dan Render Awal ---
        document.addEventListener('DOMContentLoaded', function() {
            // Perbaikan footer (jika belum ada)
            document.body.classList.add('flex', 'flex-col', 'min-h-screen');
            document.querySelector('main').classList.add('flex-grow');

            if (localStorage.getItem('userRole') !== 'admin') {
                window.location.href = '../../index.php'; // Kembali ke index.php di root
            }
            renderCategories(); // Panggil fungsi untuk menampilkan data saat halaman dimuat
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