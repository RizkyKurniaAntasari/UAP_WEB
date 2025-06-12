
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