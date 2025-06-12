// Data transaksi dummy (global agar bisa diakses fungsi lain)
        let transactionsData = [{
                id: 'TRX001',
                date: '2025-06-10',
                product: 'Laptop Gaming X1',
                type: 'masuk',
                quantity: 10,
                user: 'admin',
                notes: 'Pembelian dari PT. ABC Global'
            },
            {
                id: 'TRX002',
                date: '2025-06-10',
                product: 'Kemeja Pria Casual',
                type: 'keluar',
                quantity: 5,
                user: 'staff_gudang',
                notes: 'Penjualan ke pelanggan B'
            },
            {
                id: 'TRX003',
                date: '2025-06-09',
                product: 'Berliner Coklat',
                type: 'masuk',
                quantity: 30,
                user: 'admin',
                notes: 'Pasokan harian'
            },
            {
                id: 'TRX004',
                date: '2025-06-08',
                product: 'Mouse Wireless A10',
                type: 'keluar',
                quantity: 2,
                user: 'staff_logistik',
                notes: 'Pengiriman pesanan online'
            },
            {
                id: 'TRX005',
                date: '2025-06-07',
                product: 'Laptop Gaming X1',
                type: 'keluar',
                quantity: 1,
                user: 'admin',
                notes: 'Retur ke pemasok'
            },
            {
                id: 'TRX006',
                date: '2025-06-07',
                product: 'Air Mineral 600ml',
                type: 'masuk',
                quantity: 100,
                user: 'staff_gudang',
                notes: 'Pembelian bulk'
            },
        ];

        // Data produk dummy (untuk dropdown di form transaksi, bisa disinkronkan dari products.php aslinya)
        const productsList = [{
                name: 'Laptop Gaming X1'
            },
            {
                name: 'Kemeja Pria Casual'
            },
            {
                name: 'Berliner Coklat'
            },
            {
                name: 'Mouse Wireless A10'
            },
            {
                name: 'Celana Jeans Slim Fit'
            },
            {
                name: 'Air Mineral 600ml'
            },
        ];

        const transactionTableBody = document.getElementById('transactionTableBody');
        const transactionModal = document.getElementById('transactionModal');
        const modalTitle = document.getElementById('modalTitle');
        const transactionForm = document.getElementById('transactionForm');
        const transactionId = document.getElementById('transactionId');
        const transactionDate = document.getElementById('transactionDate');
        const transactionProduct = document.getElementById('transactionProduct');
        const transactionQuantity = document.getElementById('transactionQuantity');
        const transactionType = document.getElementById('transactionType'); // Ini untuk form
        const transactionUser = document.getElementById('transactionUser');
        const transactionNotes = document.getElementById('transactionNotes');

        const transactionTypeFilter = document.getElementById('transactionTypeFilter'); // Ini untuk filter
        const startDateFilter = document.getElementById('startDateFilter');
        const endDateFilter = document.getElementById('endDateFilter');

        let currentEditingId = null; // Untuk melacak transaksi yang sedang diedit

        // Fungsi untuk menampilkan data transaksi ke tabel
        function renderTransactions(filteredData = transactionsData) {
            transactionTableBody.innerHTML = ''; // Bersihkan tabel sebelum render ulang
            if (filteredData.length === 0) {
                transactionTableBody.innerHTML = `<tr><td colspan="8" class="py-4 px-6 text-center text-gray-500">Tidak ada transaksi yang ditemukan.</td></tr>`;
                return;
            }

            filteredData.forEach(transaction => {
                const row = `
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">${transaction.id}</td>
                        <td class="py-3 px-6 text-left">${transaction.date}</td>
                        <td class="py-3 px-6 text-left">${transaction.product}</td>
                        <td class="py-3 px-6 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                ${transaction.type === 'masuk' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'}
                            ">${transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)}</span>
                        </td>
                        <td class="py-3 px-6 text-center">${transaction.quantity}</td>
                        <td class="py-3 px-6 text-left">${transaction.user}</td>
                        <td class="py-3 px-6 text-left">${transaction.notes}</td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <button class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Edit" onclick="openEditModal('${transaction.id}')">
                                    ✏️
                                </button>
                                <button class="w-6 h-6 transform hover:text-red-500 hover:scale-110" title="Hapus" onclick="deleteTransaction('${transaction.id}')">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                transactionTableBody.innerHTML += row;
            });
        }

        // Fungsi untuk mengisi dropdown produk
        function populateProductDropdown() {
            transactionProduct.innerHTML = ''; // Bersihkan dropdown
            productsList.forEach(product => {
                const option = document.createElement('option');
                option.value = product.name;
                option.textContent = product.name;
                transactionProduct.appendChild(option);
            });
        }

        // --- Fungsionalitas Hapus (Delete) ---
        function deleteTransaction(id) {
            if (confirm(`Apakah Anda yakin ingin menghapus transaksi dengan ID ${id}?`)) {
                transactionsData = transactionsData.filter(transaction => transaction.id !== id);
                filterTransactions(); // Render ulang dengan filter yang aktif
            }
        }

        // --- Fungsionalitas Tambah (Create) & Edit (Update) via Modal ---
        function openAddModal() {
            modalTitle.textContent = 'Buat Transaksi Baru';
            transactionForm.reset(); // Kosongkan formulir
            transactionId.value = ''; // Pastikan ID kosong untuk mode tambah
            currentEditingId = null;
            transactionUser.value = localStorage.getItem('userUsername') || 'Admin'; // Ambil username dari localStorage jika ada, atau default
            // Set tanggal default hari ini
            transactionDate.valueAsDate = new Date();
            populateProductDropdown(); // Isi dropdown produk
            transactionModal.style.display = 'flex'; // Tampilkan modal
        }

        function openEditModal(id) {
            modalTitle.textContent = 'Edit Transaksi';
            const transactionToEdit = transactionsData.find(transaction => transaction.id === id);
            if (transactionToEdit) {
                transactionId.value = transactionToEdit.id;
                transactionDate.value = transactionToEdit.date;
                populateProductDropdown(); // Isi dropdown produk sebelum set nilai
                transactionProduct.value = transactionToEdit.product;
                transactionQuantity.value = transactionToEdit.quantity;
                transactionType.value = transactionToEdit.type;
                transactionUser.value = transactionToEdit.user;
                transactionNotes.value = transactionToEdit.notes;
                currentEditingId = id; // Simpan ID transaksi yang sedang diedit
                transactionModal.style.display = 'flex'; // Tampilkan modal
            }
        }

        function closeModal() {
            transactionModal.style.display = 'none'; // Sembunyikan modal
        }

        transactionForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form submit default

            const id = transactionId.value; // ID sudah string
            const date = transactionDate.value;
            const product = transactionProduct.value;
            const quantity = parseInt(transactionQuantity.value);
            const type = transactionType.value;
            const user = transactionUser.value;
            const notes = transactionNotes.value;

            if (currentEditingId) {
                // Mode Edit (Update)
                const transactionIndex = transactionsData.findIndex(trans => trans.id === id);
                if (transactionIndex !== -1) {
                    transactionsData[transactionIndex] = {
                        id: id,
                        date: date,
                        product: product,
                        type: type,
                        quantity: quantity,
                        user: user,
                        notes: notes
                    };
                }
            } else {
                // Mode Tambah (Create)
                // Generate ID unik baru (contoh sederhana, bisa lebih kompleks)
                const newIdNum = transactionsData.length > 0 ? parseInt(transactionsData[0].id.replace('TRX', '')) + 1 : 1;
                const newId = 'TRX' + String(newIdNum).padStart(3, '0'); // Contoh: TRX007

                transactionsData.unshift({ // Tambahkan ke awal array
                    id: newId,
                    date: date,
                    product: product,
                    type: type,
                    quantity: quantity,
                    user: user,
                    notes: notes
                });
            }
            filterTransactions(); // Render ulang tabel setelah penambahan/pembaruan (dengan filter yang aktif)
            closeModal(); // Tutup modal
        });

        // --- Fungsionalitas Filter ---
        function filterTransactions() {
            const typeFilter = transactionTypeFilter.value;
            const startDate = startDateFilter.value;
            const endDate = endDateFilter.value;

            let filtered = transactionsData;

            if (typeFilter) {
                filtered = filtered.filter(trans => trans.type === typeFilter);
            }
            if (startDate) {
                filtered = filtered.filter(trans => trans.date >= startDate);
            }
            if (endDate) {
                filtered = filtered.filter(trans => trans.date <= endDate);
            }
            renderTransactions(filtered); // Panggil render dengan data yang sudah difilter
        }

        // --- Logika Autentikasi dan Render Awal ---
        document.addEventListener('DOMContentLoaded', function() {
            // Perbaikan footer (jika belum ada)
            document.body.classList.add('flex', 'flex-col', 'min-h-screen');
            document.querySelector('main').classList.add('flex-grow');

            if (localStorage.getItem('userRole') !== 'admin') {
                // window.location.href = '../../index.php'; // Kembali ke index.php di root
            }
            renderTransactions(); // Panggil fungsi untuk menampilkan data saat halaman dimuat
        });

        // Fungsi logout client-side (tetap sama)
        function logoutClientSide(event) {
            event.preventDefault();
            localStorage.removeItem('userRole');
            localStorage.removeItem('userEmail');
            // window.location.href = '../../logout.php'; // Path ke logout.php di root
        }
