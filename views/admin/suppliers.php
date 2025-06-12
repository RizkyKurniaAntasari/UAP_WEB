<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pemasok - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="bg-gray-100 font-sans flex flex-col min-h-screen">

    <?php include_once 'components/navbar.php'?>

    <main class="container mx-auto px-6 py-8 flex-grow"> <h1 class="text-4xl font-bold text-gray-800 mb-6">Manajemen Pemasok</h1>
        <p class="text-gray-700 mb-8">Kelola daftar pemasok yang menyediakan barang untuk inventaris.</p>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Daftar Pemasok</h2>
                <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300" onclick="openAddModal()">Tambah Pemasok Baru</a>
            </div>

            <div class="mb-4">
                <input type="text" id="supplierSearch" placeholder="Cari nama pemasok..." class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Nama Pemasok</th>
                            <th class="py-3 px-6 text-left">Kontak Person</th>
                            <th class="py-3 px-6 text-left">Telepon</th>
                            <th class="py-3 px-6 text-left">Alamat</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light" id="supplierTableBody">
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

    <div id="supplierModal" class="modal hidden">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800 mb-6">Tambah Pemasok Baru</h2>
            <form id="supplierForm">
                <input type="hidden" id="supplierId">
                <div class="mb-4">
                    <label for="supplierName" class="block text-gray-700 text-sm font-semibold mb-2">Nama Pemasok</label>
                    <input type="text" id="supplierName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="supplierContact" class="block text-gray-700 text-sm font-semibold mb-2">Kontak Person</label>
                    <input type="text" id="supplierContact" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="supplierPhone" class="block text-gray-700 text-sm font-semibold mb-2">Telepon</label>
                    <input type="tel" id="supplierPhone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-6">
                    <label for="supplierAddress" class="block text-gray-700 text-sm font-semibold mb-2">Alamat</label>
                    <textarea id="supplierAddress" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-800 px-5 py-2 rounded-md hover:bg-gray-400 transition duration-300">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition duration-300">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Data pemasok dummy (global agar bisa diakses fungsi lain)
        let suppliersData = [
            { id: 1, name: 'PT. ABC Global', contact: 'Budi Santoso', phone: '081234567890', address: 'Jl. Merdeka No. 10, Jakarta' },
            { id: 2, name: 'CV. Jaya Mandiri', contact: 'Siti Aminah', phone: '085678901234', address: 'Jl. Harmoni Indah No. 5, Bandung' },
            { id: 3, name: 'UMKM Donut Sejahtera', contact: 'Pak Dono', phone: '087811223344', address: 'Jl. Roti Manis No. 20, Surabaya' },
            { id: 4, name: 'PT. Tirta Segar', contact: 'Dewi Lestari', phone: '081199887766', address: 'Jl. Air Sehat No. 30, Semarang' }
        ];

        const supplierTableBody = document.getElementById('supplierTableBody');
        const supplierModal = document.getElementById('supplierModal');
        const modalTitle = document.getElementById('modalTitle');
        const supplierForm = document.getElementById('supplierForm');
        const supplierId = document.getElementById('supplierId');
        const supplierName = document.getElementById('supplierName');
        const supplierContact = document.getElementById('supplierContact');
        const supplierPhone = document.getElementById('supplierPhone');
        const supplierAddress = document.getElementById('supplierAddress');
        let currentEditingId = null; // Untuk melacak pemasok yang sedang diedit

        // Fungsi untuk menampilkan data pemasok ke tabel
        function renderSuppliers() {
            supplierTableBody.innerHTML = ''; // Bersihkan tabel sebelum render ulang
            suppliersData.forEach(supplier => {
                const row = `
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">${supplier.id}</td>
                        <td class="py-3 px-6 text-left">${supplier.name}</td>
                        <td class="py-3 px-6 text-left">${supplier.contact}</td>
                        <td class="py-3 px-6 text-left">${supplier.phone}</td>
                        <td class="py-3 px-6 text-left">${supplier.address}</td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <button class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Edit" onclick="openEditModal(${supplier.id})">
                                    ✏️
                                </button>
                                <button class="w-6 h-6 transform hover:text-red-500 hover:scale-110" title="Hapus" onclick="deleteSupplier(${supplier.id})">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                supplierTableBody.innerHTML += row;
            });
        }

        // --- Fungsionalitas Hapus (Delete) ---
        function deleteSupplier(id) {
            if (confirm(`Apakah Anda yakin ingin menghapus pemasok dengan ID ${id}?`)) {
                suppliersData = suppliersData.filter(supplier => supplier.id !== id);
                renderSuppliers(); // Render ulang tabel setelah penghapusan
            }
        }

        // --- Fungsionalitas Tambah (Create) & Edit (Update) via Modal ---
        function openAddModal() {
            modalTitle.textContent = 'Tambah Pemasok Baru';
            supplierForm.reset(); // Kosongkan formulir
            supplierId.value = ''; // Pastikan ID kosong untuk mode tambah
            currentEditingId = null;
            supplierModal.style.display = 'flex'; // Tampilkan modal
        }

        function openEditModal(id) {
            modalTitle.textContent = 'Edit Pemasok';
            const supplierToEdit = suppliersData.find(supplier => supplier.id === id);
            if (supplierToEdit) {
                supplierId.value = supplierToEdit.id;
                supplierName.value = supplierToEdit.name;
                supplierContact.value = supplierToEdit.contact;
                supplierPhone.value = supplierToEdit.phone;
                supplierAddress.value = supplierToEdit.address;
                currentEditingId = id; // Simpan ID pemasok yang sedang diedit
                supplierModal.style.display = 'flex'; // Tampilkan modal
            }
        }

        function closeModal() {
            supplierModal.style.display = 'none'; // Sembunyikan modal
        }

        supplierForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form submit default

            const id = supplierId.value ? parseInt(supplierId.value) : null;
            const name = supplierName.value;
            const contact = supplierContact.value;
            const phone = supplierPhone.value;
            const address = supplierAddress.value;

            if (currentEditingId) {
                // Mode Edit (Update)
                const supplierIndex = suppliersData.findIndex(sup => sup.id === id);
                if (supplierIndex !== -1) {
                    suppliersData[supplierIndex] = {
                        id: id,
                        name: name,
                        contact: contact,
                        phone: phone,
                        address: address
                    };
                }
            } else {
                // Mode Tambah (Create)
                const newId = suppliersData.length > 0 ? Math.max(...suppliersData.map(sup => sup.id)) + 1 : 1;
                suppliersData.push({
                    id: newId,
                    name: name,
                    contact: contact,
                    phone: phone,
                    address: address
                });
            }
            renderSuppliers(); // Render ulang tabel setelah penambahan/pembaruan
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
            renderSuppliers(); // Panggil fungsi untuk menampilkan data saat halaman dimuat
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