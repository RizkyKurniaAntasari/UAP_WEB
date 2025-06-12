<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Transaksi - Admin Dashboard</title>
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

    <?php include_once 'components/navbar.php' ?>

    <main class="container mx-auto px-6 py-8 flex-grow">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Manajemen Transaksi</h1>
        <p class="text-gray-700 mb-8">Pantau semua transaksi masuk dan keluar barang dari gudang.</p>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Riwayat Transaksi</h2>
                <button id="addTransactionBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">Buat Transaksi Baru</button>
            </div>

            <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="transactionTypeFilter" class="block text-gray-700 text-sm font-semibold mb-1">Jenis Transaksi:</label>
                    <select id="transactionTypeFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <option value="">Semua</option>
                        <option value="masuk">Masuk</option>
                        <option value="keluar">Keluar</option>
                    </select>
                </div>
                <div>
                    <label for="startDateFilter" class="block text-gray-700 text-sm font-semibold mb-1">Dari Tanggal:</label>
                    <input type="date" id="startDateFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                </div>
                <div>
                    <label for="endDateFilter" class="block text-gray-700 text-sm font-semibold mb-1">Sampai Tanggal:</label>
                    <input type="date" id="endDateFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID Transaksi</th>
                            <th class="py-3 px-6 text-left">Tanggal</th>
                            <th class="py-3 px-6 text-left">Nama Barang</th>
                            <th class="py-3 px-6 text-center">Jenis</th>
                            <th class="py-3 px-6 text-center">Kuantitas</th>
                            <th class="py-3 px-6 text-left">Oleh User</th>
                            <th class="py-3 px-6 text-left">Catatan</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light" id="transactionTableBody">
                        </tbody>
                </table>
            </div>

            <div class="flex justify-center mt-6 space-x-2" id="paginationContainer">
                </div>

        </div>
    </main>

    <?php include_once 'components/footer.php' ?>

    <div id="transactionModal" class="modal hidden">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800 mb-6">Buat Transaksi Baru</h2>
            <form id="transactionForm">
                <input type="hidden" id="transactionId">
                <div class="mb-4">
                    <label for="transactionDate" class="block text-gray-700 text-sm font-semibold mb-2">Tanggal</label>
                    <input type="date" id="transactionDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="transactionProduct" class="block text-gray-700 text-sm font-semibold mb-2">Nama Barang</label>
                    <input type="text" id="transactionProduct" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ketik nama barang..." required>
                </div>
                <div class="mb-4">
                    <label for="transactionQuantity" class="block text-gray-700 text-sm font-semibold mb-2">Kuantitas</label>
                    <input type="number" id="transactionQuantity" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required min="1">
                </div>
                <div class="mb-4">
                    <label for="transactionTypeModal" class="block text-gray-700 text-sm font-semibold mb-2">Jenis Transaksi</label>
                    <select id="transactionTypeModal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="masuk">Masuk</option>
                        <option value="keluar">Keluar</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="transactionUser" class="block text-gray-700 text-sm font-semibold mb-2">Oleh User</label>
                    <input type="text" id="transactionUser" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                </div>
                <div class="mb-6">
                    <label for="transactionNotes" class="block text-gray-700 text-sm font-semibold mb-2">Catatan</label>
                    <textarea id="transactionNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
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
        let allTransactionsData = [
            { id: 'TRX001', date: '2025-06-10', product: 'Laptop Gaming X1', type: 'masuk', quantity: 10, user: 'admin', notes: 'Pembelian dari PT. ABC Global' },
            { id: 'TRX002', date: '2025-06-10', product: 'Kemeja Pria Casual', type: 'keluar', quantity: 5, user: 'staff_gudang', notes: 'Penjualan ke pelanggan B' },
            { id: 'TRX003', date: '2025-06-09', product: 'Berliner Coklat', type: 'masuk', quantity: 30, user: 'admin', notes: 'Pasokan harian' },
            { id: 'TRX004', date: '2025-06-08', product: 'Mouse Wireless A10', type: 'keluar', quantity: 2, user: 'staff_logistik', notes: 'Pengiriman pesanan online' },
            { id: 'TRX005', date: '2025-06-07', product: 'Laptop Gaming X1', type: 'keluar', quantity: 1, user: 'admin', notes: 'Retur ke pemasok' },
            { id: 'TRX006', date: '2025-06-07', product: 'Air Mineral 600ml', type: 'masuk', quantity: 100, user: 'staff_gudang', notes: 'Pembelian bulk' },
            { id: 'TRX007', date: '2025-06-06', product: 'Celana Jeans Slim Fit', type: 'masuk', quantity: 20, user: 'admin', notes: 'Restock mingguan' },
            { id: 'TRX008', date: '2025-06-05', product: 'Buku Resep Masakan', type: 'keluar', quantity: 3, user: 'staff_penjualan', notes: 'Gift untuk pelanggan premium' },
            { id: 'TRX009', date: '2025-06-04', product: 'Kopi Bubuk Robusta', type: 'masuk', quantity: 50, user: 'admin', notes: 'Pembelian dari PT. Kopi Nikmat' },
            { id: 'TRX010', date: '2025-06-03', product: 'Mouse Wireless A10', type: 'masuk', quantity: 15, user: 'admin', notes: 'Penggantian unit rusak' },
            { id: 'TRX011', date: '2025-06-02', product: 'Kemeja Pria Casual', type: 'masuk', quantity: 10, user: 'staff_gudang', notes: 'Tambahan stok dadakan' },
            { id: 'TRX012', date: '2025-06-01', product: 'Berliner Coklat', type: 'keluar', quantity: 5, user: 'staff_logistik', notes: 'Expired, dibuang' }
        ];

        // productsList tidak lagi digunakan untuk dropdown, tapi bisa tetap ada untuk referensi atau autocomplete
        // const productsList = [
        //     { name: 'Laptop Gaming X1' }, { name: 'Kemeja Pria Casual' }, { name: 'Berliner Coklat' },
        //     { name: 'Mouse Wireless A10' }, { name: 'Celana Jeans Slim Fit' }, { name: 'Air Mineral 600ml' },
        //     { name: 'Buku Resep Masakan' }, { name: 'Kopi Bubuk Robusta' }
        // ];

        let filteredTransactions = []; // Data setelah disaring
        let currentPage = 1;
        const itemsPerPage = 5; // Jumlah item per halaman

        // --- DOM Elements ---
        const transactionTableBody = document.getElementById('transactionTableBody');
        const paginationContainer = document.getElementById('paginationContainer');
        const addTransactionBtn = document.getElementById('addTransactionBtn');

        // Filter Elements
        const transactionTypeFilter = document.getElementById('transactionTypeFilter');
        const startDateFilter = document.getElementById('startDateFilter');
        const endDateFilter = document.getElementById('endDateFilter');

        // Modal Elements
        const transactionModal = document.getElementById('transactionModal');
        const modalTitle = document.getElementById('modalTitle');
        const transactionForm = document.getElementById('transactionForm');
        const transactionIdInput = document.getElementById('transactionId');
        const transactionDateInput = document.getElementById('transactionDate');
        const transactionProductInput = document.getElementById('transactionProduct'); // Ini sekarang input teks
        const transactionQuantityInput = document.getElementById('transactionQuantity');
        const transactionTypeModalInput = document.getElementById('transactionTypeModal');
        const transactionUserInput = document.getElementById('transactionUser');
        const transactionNotesInput = document.getElementById('transactionNotes');

        // --- Fungsi Helper ---

        // Fungsi untuk merender transaksi ke tabel
        function renderTransactions() {
            transactionTableBody.innerHTML = ''; // Bersihkan tabel sebelum render ulang

            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedTransactions = filteredTransactions.slice(start, end);

            if (paginatedTransactions.length === 0) {
                transactionTableBody.innerHTML = `<tr><td colspan="8" class="py-4 px-6 text-center text-gray-500">Tidak ada transaksi yang ditemukan.</td></tr>`;
                renderPagination();
                return;
            }

            paginatedTransactions.forEach(transaction => {
                const typeColorClass = transaction.type === 'masuk' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800';
                const row = `
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">${transaction.id}</td>
                        <td class="py-3 px-6 text-left">${transaction.date}</td>
                        <td class="py-3 px-6 text-left">${transaction.product}</td>
                        <td class="py-3 px-6 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold ${typeColorClass}">
                                ${transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)}
                            </span>
                        </td>
                        <td class="py-3 px-6 text-center">${transaction.quantity}</td>
                        <td class="py-3 px-6 text-left">${transaction.user}</td>
                        <td class="py-3 px-6 text-left">${transaction.notes}</td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <button onclick="openModal('${transaction.id}')" class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Edit">
                                    ✏️
                                </button>
                                <button onclick="deleteTransaction('${transaction.id}')" class="w-6 h-6 transform hover:text-red-500 hover:scale-110" title="Hapus">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                transactionTableBody.innerHTML += row;
            });
            renderPagination();
        }

        // Fungsi untuk merender kontrol paginasi
        function renderPagination() {
            paginationContainer.innerHTML = '';
            const totalPages = Math.ceil(filteredTransactions.length / itemsPerPage);

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
                    renderTransactions();
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

        // --- Fungsionalitas Filter & Pencarian Tanggal (Otomatis) ---
        function filterTransactions() {
            const typeFilter = transactionTypeFilter.value;
            const startDate = startDateFilter.value;
            const endDate = endDateFilter.value;

            filteredTransactions = allTransactionsData.filter(transaction => {
                const matchesType = typeFilter === '' || transaction.type === typeFilter;
                const matchesStartDate = !startDate || transaction.date >= startDate;
                const matchesEndDate = !endDate || transaction.date <= endDate;

                return matchesType && matchesStartDate && matchesEndDate;
            });

            currentPage = 1; // Reset ke halaman pertama setelah filter
            renderTransactions();
        }

        // --- Fungsionalitas CRUD (Tambah, Edit, Hapus) ---

        // Fungsi untuk membuka modal (Tambah atau Edit)
        window.openModal = function(id = null) { // Dibuat global untuk onclick HTML
            transactionForm.reset(); // Kosongkan formulir
            transactionIdInput.value = ''; // Pastikan ID kosong untuk mode tambah

            if (id) {
                modalTitle.textContent = 'Edit Transaksi';
                const transactionToEdit = allTransactionsData.find(trans => trans.id === id);
                if (transactionToEdit) {
                    transactionIdInput.value = transactionToEdit.id;
                    transactionDateInput.value = transactionToEdit.date;
                    transactionProductInput.value = transactionToEdit.product; // Ambil nilai untuk input teks
                    transactionQuantityInput.value = transactionToEdit.quantity;
                    transactionTypeModalInput.value = transactionToEdit.type;
                    transactionUserInput.value = transactionToEdit.user;
                    transactionNotesInput.value = transactionToEdit.notes;
                }
            } else {
                modalTitle.textContent = 'Buat Transaksi Baru';
                transactionDateInput.valueAsDate = new Date(); // Set tanggal default hari ini
                // Ambil username dari localStorage jika ada, atau default
                transactionUserInput.value = localStorage.getItem('userUsername') || 'admin'; 
            }
            transactionModal.classList.remove('hidden'); // Tampilkan modal
        }

        // Fungsi untuk menutup modal
        window.closeModal = function() { // Dibuat global untuk onclick HTML
            transactionModal.classList.add('hidden'); // Sembunyikan modal
        }

        // Fungsi untuk menghapus transaksi
        window.deleteTransaction = function(id) { // Dibuat global untuk onclick HTML
            if (confirm(`Apakah Anda yakin ingin menghapus transaksi dengan ID ${id}?`)) {
                allTransactionsData = allTransactionsData.filter(transaction => transaction.id !== id);
                filterTransactions(); // Render ulang dengan filter yang aktif
                alert('Transaksi berhasil dihapus.');
            }
        }

        // Handle submit form transaksi (Tambah atau Edit)
        transactionForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form submit default

            const id = transactionIdInput.value; // ID bisa string (TRX001)
            const date = transactionDateInput.value;
            const product = transactionProductInput.value.trim(); // Ambil dari input teks, trim spasi
            const quantity = parseInt(transactionQuantityInput.value);
            const type = transactionTypeModalInput.value;
            const user = transactionUserInput.value;
            const notes = transactionNotesInput.value;

            // Basic validation
            if (!date || !product || isNaN(quantity) || quantity <= 0 || !type || !user) {
                alert('Mohon isi semua field wajib dengan benar.');
                return;
            }

            if (id) {
                // Mode Edit (Update)
                const transactionIndex = allTransactionsData.findIndex(trans => trans.id === id);
                if (transactionIndex !== -1) {
                    allTransactionsData[transactionIndex] = {
                        id: id, date, product, type, quantity, user, notes
                    };
                    alert(`Transaksi '${id}' berhasil diperbarui.`);
                }
            } else {
                // Mode Tambah (Create)
                // Generate ID unik baru (contoh sederhana: TRX + nomor urut terbesar + 1)
                // Pastikan allTransactionsData diurutkan menurun berdasarkan ID untuk menemukan max ID
                const sortedTransactions = [...allTransactionsData].sort((a, b) => {
                    const idA = parseInt(a.id.replace('TRX', '')) || 0;
                    const idB = parseInt(b.id.replace('TRX', '')) || 0;
                    return idB - idA;
                });
                const lastIdNum = sortedTransactions.length > 0 ? (parseInt(sortedTransactions[0].id.replace('TRX', '')) || 0) : 0;
                const newId = 'TRX' + String(lastIdNum + 1).padStart(3, '0');

                allTransactionsData.unshift({ // Tambahkan ke awal array agar yang baru muncul duluan
                    id: newId, date, product, type, quantity, user, notes
                });
                alert(`Transaksi '${newId}' berhasil ditambahkan.`);
            }
            filterTransactions(); // Render ulang tabel setelah penambahan/pembaruan (dengan filter yang aktif)
            closeModal(); // Tutup modal
        });


        // --- Event Listeners Awal ---
        document.addEventListener('DOMContentLoaded', function() {
            // Autentikasi
            if (localStorage.getItem('userRole') !== 'admin') {
                window.location.href = '../../index.php'; // Kembali ke index.php di root
                return;
            }

            // Set default username di modal jika ada dari localStorage (asumsi 'admin' atau username lain)
            // if (localStorage.getItem('userUsername')) { // Asumsi Anda menyimpan username di localStorage
            //     transactionUserInput.value = localStorage.getItem('userUsername');
            // } else {
                 transactionUserInput.value = 'admin'; // Default jika tidak ada di localStorage
            // }


            // Initial render: Panggil filterTransactions() saat halaman dimuat
            filterTransactions(); // Ini akan memanggil renderTransactions() dan renderPagination()

            // Event listener untuk tombol "Buat Transaksi Baru"
            addTransactionBtn.addEventListener('click', () => openModal(null));

            // Event listeners untuk filter (langsung filter saat diubah)
            transactionTypeFilter.addEventListener('change', filterTransactions);
            startDateFilter.addEventListener('change', filterTransactions);
            endDateFilter.addEventListener('change', filterTransactions);
        });

        // Fungsi logout client-side (dari components/navbar.php atau di sini jika tidak termasuk)
        function logoutClientSide(event) {
            event.preventDefault();
            localStorage.removeItem('userRole');
            localStorage.removeItem('userEmail');
            // Jika Anda juga menyimpan username, hapus juga
            localStorage.removeItem('userUsername'); 
            window.location.href = '../../logout.php';
        }
    </script>
</body>

</html>