<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Pemasok Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Gaya tambahan untuk modal */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .modal-content {
            z-index: 1000;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans flex flex-col min-h-screen">

    <nav class="bg-green-700 p-4 shadow-md text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="dashboard.php" class="text-2xl font-bold">Pemasok Dashboard</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:text-green-200">Beranda</a>
                <a href="my_products.php" class="hover:text-green-200">Produk Saya</a>
                <a href="orders.php" class="hover:text-green-200 font-semibold">Pesanan</a>
                <a href="../../logout.php" class="bg-red-600 px-3 py-1 rounded-md hover:bg-red-700 transition duration-300" onclick="logoutClientSide(event)">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-8 flex-grow">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Daftar Pesanan</h1>
        <p class="text-gray-700 mb-8">Lihat pesanan yang melibatkan produk Anda dan statusnya.</p>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Pesanan Masuk</h2>
            </div>

            <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="orderStatus" class="block text-gray-700 text-sm font-semibold mb-1">Status Pesanan:</label>
                    <select id="orderStatus" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 w-full">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="diproses">Diproses</option>
                        <option value="dikirim">Dikirim</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label for="orderSearch" class="block text-gray-700 text-sm font-semibold mb-1">Cari Pesanan:</label>
                    <input type="text" id="orderSearch" placeholder="Cari ID/Nama Produk..." class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 w-full">
                </div>
                <div class="md:col-span-2 text-right">
                    <button id="filterOrdersBtn" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300">Filter Pesanan</button>
                    <button id="resetFilterBtn" class="bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500 transition duration-300 ml-2 hidden">Reset Filter</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID Pesanan</th>
                            <th class="py-3 px-6 text-left">Tanggal Pesanan</th>
                            <th class="py-3 px-6 text-left">Nama Produk</th>
                            <th class="py-3 px-6 text-center">Kuantitas</th>
                            <th class="py-3 px-6 text-right">Total Harga</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light" id="orderTableBody">
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

    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center modal-overlay">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md modal-content">
            <h3 class="text-2xl font-bold mb-6 text-gray-800">Detail Pesanan</h3>
            <div class="space-y-4 text-gray-700">
                <p><strong>ID Pesanan:</strong> <span id="detailOrderId"></span></p>
                <p><strong>Tanggal Pesanan:</strong> <span id="detailOrderDate"></span></p>
                <p><strong>Nama Produk:</strong> <span id="detailProductName"></span></p>
                <p><strong>Kuantitas:</strong> <span id="detailQuantity"></span></p>
                <p><strong>Total Harga:</strong> <span id="detailTotalPrice"></span></p>
                <p><strong>Status:</strong> <span id="detailStatus" class="font-semibold"></span></p>
                <p><strong>Pemasok Email:</strong> <span id="detailSupplierEmail"></span></p>
            </div>
            <div class="mt-8 text-right">
                <button onclick="closeDetailModal()" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-md hover:bg-gray-400 font-semibold">Tutup</button>
            </div>
        </div>
    </div>

    <div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center modal-overlay">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-sm modal-content">
            <h3 class="text-2xl font-bold mb-6 text-gray-800">Ubah Status Pesanan <span id="statusModalOrderId" class="text-indigo-600"></span></h3>
            <div class="mb-4">
                <label for="newOrderStatus" class="block text-gray-700 text-sm font-bold mb-2">Pilih Status Baru:</label>
                <select id="newOrderStatus" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 w-full">
                    <option value="pending">Pending</option>
                    <option value="diproses">Diproses</option>
                    <option value="dikirim">Dikirim</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
            <div class="flex justify-end space-x-4 mt-8">
                <button onclick="closeStatusModal()" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-md hover:bg-gray-400 font-semibold">Batal</button>
                <button id="saveStatusBtn" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 font-semibold">Simpan</button>
            </div>
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

        document.addEventListener('DOMContentLoaded', function() {
            // Logika autentikasi sisi klien
            if (localStorage.getItem('userRole') !== 'pemasok') {
                // window.location.href = '../../index.php';
                return;
            }

            // Data pesanan dummy
            const rawOrdersData = [
                { id: 'ORD001', date: '2025-06-10', product: 'Kemeja Pria Casual', quantity: 2, price_per_unit: 120000, status: 'pending', supplierEmail: 'pemasok@example.com' },
                { id: 'ORD002', date: '2025-06-09', product: 'Laptop Gaming X1', quantity: 1, price_per_unit: 15000000, status: 'diproses', supplierEmail: 'ptabclobal@example.com' },
                { id: 'ORD003', date: '2025-06-08', product: 'Celana Jeans Slim Fit', quantity: 3, price_per_unit: 250000, status: 'dikirim', supplierEmail: 'pemasok@example.com' },
                { id: 'ORD004', date: '2025-06-07', product: 'Berliner Coklat', quantity: 10, price_per_unit: 15000, status: 'selesai', supplierEmail: 'umkmdonut@example.com' },
                { id: 'ORD005', date: '2025-06-06', product: 'Kemeja Pria Casual', quantity: 1, price_per_unit: 120000, status: 'dibatalkan', supplierEmail: 'pemasok@example.com' },
                { id: 'ORD006', date: '2025-06-05', product: 'Mouse Wireless A10', quantity: 2, price_per_unit: 180000, status: 'pending', supplierEmail: 'ptabclobal@example.com' },
                { id: 'ORD007', date: '2025-06-04', product: 'Celana Jeans Slim Fit', quantity: 1, price_per_unit: 250000, status: 'pending', supplierEmail: 'pemasok@example.com' },
                { id: 'ORD008', date: '2025-06-03', product: 'Kopi Bubuk Robusta', quantity: 5, price_per_unit: 25000, status: 'selesai', supplierEmail: 'pttirta@example.com' },
                { id: 'ORD009', date: '2025-06-02', product: 'Kemeja Pria Casual', quantity: 3, price_per_unit: 120000, status: 'diproses', supplierEmail: 'pemasok@example.com' },
                { id: 'ORD010', date: '2025-06-01', product: 'Laptop Gaming X1', quantity: 1, price_per_unit: 15000000, status: 'dibatalkan', supplierEmail: 'ptabclobal@example.com' },
            ];

            const currentUserEmail = localStorage.getItem('userEmail');
            let allOrdersForSupplier = rawOrdersData.filter(order => order.supplierEmail === currentUserEmail);
            let filteredOrders = [...allOrdersForSupplier]; // Data yang akan ditampilkan setelah filter
            let currentPage = 1;
            const itemsPerPage = 5; // Jumlah item per halaman

            // --- DOM Elements ---
            const orderTableBody = document.getElementById('orderTableBody');
            const orderStatusSelect = document.getElementById('orderStatus');
            const orderSearchInput = document.getElementById('orderSearch');
            const filterOrdersBtn = document.getElementById('filterOrdersBtn');
            const resetFilterBtn = document.getElementById('resetFilterBtn');
            const paginationContainer = document.getElementById('paginationContainer');

            // Modal Detail
            const detailModal = document.getElementById('detailModal');
            const detailOrderId = document.getElementById('detailOrderId');
            const detailOrderDate = document.getElementById('detailOrderDate');
            const detailProductName = document.getElementById('detailProductName');
            const detailQuantity = document.getElementById('detailQuantity');
            const detailTotalPrice = document.getElementById('detailTotalPrice');
            const detailStatus = document.getElementById('detailStatus');
            const detailSupplierEmail = document.getElementById('detailSupplierEmail');

            // Modal Status
            const statusModal = document.getElementById('statusModal');
            const statusModalOrderId = document.getElementById('statusModalOrderId');
            const newOrderStatusSelect = document.getElementById('newOrderStatus');
            const saveStatusBtn = document.getElementById('saveStatusBtn');
            let currentOrderBeingEdited = null; // Untuk menyimpan referensi pesanan yang sedang diubah

            // --- Fungsi Helper ---

            // Fungsi untuk memformat harga ke Rupiah
            function formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            }

            // Fungsi untuk merender pesanan ke tabel
            function renderOrders() {
                orderTableBody.innerHTML = ''; // Bersihkan tabel sebelum render ulang

                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const paginatedOrders = filteredOrders.slice(start, end);

                if (paginatedOrders.length === 0) {
                    orderTableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="py-4 px-6 text-center text-gray-500">Tidak ada pesanan yang ditemukan.</td>
                        </tr>
                    `;
                    renderPagination(); // Tetap render paginasi
                    return;
                }

                paginatedOrders.forEach(order => {
                    const total_price = order.quantity * order.price_per_unit;
                    let statusColorClass = '';
                    switch(order.status) {
                        case 'pending': statusColorClass = 'bg-yellow-200 text-yellow-800'; break;
                        case 'diproses': statusColorClass = 'bg-blue-200 text-blue-800'; break;
                        case 'dikirim': statusColorClass = 'bg-purple-200 text-purple-800'; break;
                        case 'selesai': statusColorClass = 'bg-green-200 text-green-800'; break;
                        case 'dibatalkan': statusColorClass = 'bg-red-200 text-red-800'; break;
                        default: statusColorClass = 'bg-gray-200 text-gray-800'; break;
                    }

                    const row = `
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">${order.id}</td>
                            <td class="py-3 px-6 text-left">${order.date}</td>
                            <td class="py-3 px-6 text-left">${order.product}</td>
                            <td class="py-3 px-6 text-center">${order.quantity}</td>
                            <td class="py-3 px-6 text-right">${formatRupiah(total_price)}</td>
                            <td class="py-3 px-6 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold ${statusColorClass}">
                                    ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center space-x-2">
                                    <button onclick="viewOrderDetail('${order.id}')" class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Lihat Detail">
                                        👁️
                                    </button>
                                    <button onclick="changeOrderStatus('${order.id}')" class="w-6 h-6 transform hover:text-green-500 hover:scale-110" title="Ubah Status">
                                        🔄
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    orderTableBody.innerHTML += row;
                });
                renderPagination();
            }

            // Fungsi untuk merender kontrol paginasi
            function renderPagination() {
                paginationContainer.innerHTML = '';
                const totalPages = Math.ceil(filteredOrders.length / itemsPerPage);

                if (totalPages <= 1 && orderSearchInput.value.trim() === '' && orderStatusSelect.value === '') {
                    // Jangan tampilkan paginasi jika hanya ada 1 halaman dan tidak ada filter/pencarian
                    return;
                }

                const createPageLink = (page, text, isActive = false) => {
                    const link = document.createElement('a');
                    link.href = '#'; // Href kosong karena dihandle JS
                    link.className = `px-4 py-2 border rounded-md hover:bg-gray-200 ${isActive ? 'border-green-500 bg-green-500 text-white' : 'border-gray-300'}`;
                    link.textContent = text;
                    link.onclick = (e) => {
                        e.preventDefault();
                        currentPage = page;
                        renderOrders();
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

            // Fungsi untuk menerapkan filter
            function applyFilters() {
                const statusFilter = orderStatusSelect.value.toLowerCase();
                const searchTerm = orderSearchInput.value.toLowerCase().trim();

                filteredOrders = allOrdersForSupplier.filter(order => {
                    const matchesStatus = statusFilter === '' || order.status.toLowerCase() === statusFilter;
                    const matchesSearch = searchTerm === '' ||
                                          order.id.toLowerCase().includes(searchTerm) ||
                                          order.product.toLowerCase().includes(searchTerm);
                    return matchesStatus && matchesSearch;
                });

                currentPage = 1; // Reset ke halaman pertama setelah filter
                renderOrders();

                // Tampilkan tombol reset jika ada filter aktif
                if (statusFilter !== '' || searchTerm !== '') {
                    resetFilterBtn.classList.remove('hidden');
                } else {
                    resetFilterBtn.classList.add('hidden');
                }
            }

            // Fungsi untuk mereset filter
            function resetFilters() {
                orderStatusSelect.value = ''; // Reset dropdown
                orderSearchInput.value = ''; // Reset input pencarian
                applyFilters(); // Terapkan filter yang sudah direset
                resetFilterBtn.classList.add('hidden'); // Sembunyikan tombol reset
            }

            // --- Fungsi Aksi Tombol (Lihat Detail, Ubah Status) ---

            /**
             * Menampilkan modal detail pesanan.
             * @param {string} orderId - ID pesanan yang akan ditampilkan detailnya.
             */
            window.viewOrderDetail = function(orderId) {
                const order = allOrdersForSupplier.find(o => o.id === orderId);
                if (order) {
                    detailOrderId.textContent = order.id;
                    detailOrderDate.textContent = order.date;
                    detailProductName.textContent = order.product;
                    detailQuantity.textContent = order.quantity;
                    detailTotalPrice.textContent = formatRupiah(order.quantity * order.price_per_unit);
                    detailStatus.textContent = order.status.charAt(0).toUpperCase() + order.status.slice(1);
                    // Update warna status di modal
                    detailStatus.className = 'font-semibold ' + getStatusColorClass(order.status);
                    detailSupplierEmail.textContent = order.supplierEmail;

                    detailModal.classList.remove('hidden');
                } else {
                    alert(`Pesanan dengan ID ${orderId} tidak ditemukan.`);
                }
            };

            /**
             * Menutup modal detail pesanan.
             */
            window.closeDetailModal = function() {
                detailModal.classList.add('hidden');
            };

            /**
             * Menampilkan modal untuk mengubah status pesanan.
             * @param {string} orderId - ID pesanan yang statusnya akan diubah.
             */
            window.changeOrderStatus = function(orderId) {
                const order = allOrdersForSupplier.find(o => o.id === orderId);
                if (order) {
                    currentOrderBeingEdited = order; // Simpan referensi pesanan
                    statusModalOrderId.textContent = order.id;
                    newOrderStatusSelect.value = order.status; // Set dropdown ke status saat ini
                    statusModal.classList.remove('hidden');
                } else {
                    alert(`Pesanan dengan ID ${orderId} tidak ditemukan.`);
                }
            };

            /**
             * Menutup modal ubah status pesanan.
             */
            window.closeStatusModal = function() {
                statusModal.classList.add('hidden');
                currentOrderBeingEdited = null; // Hapus referensi
            };

            /**
             * Fungsi pembantu untuk mendapatkan kelas warna status
             */
            function getStatusColorClass(status) {
                switch(status) {
                    case 'pending': return 'text-yellow-800';
                    case 'diproses': return 'text-blue-800';
                    case 'dikirim': return 'text-purple-800';
                    case 'selesai': return 'text-green-800';
                    case 'dibatalkan': return 'text-red-800';
                    default: return 'text-gray-800';
                }
            }


            // Event listener untuk tombol "Simpan" di modal ubah status
            saveStatusBtn.addEventListener('click', function() {
                if (currentOrderBeingEdited) {
                    const newStatus = newOrderStatusSelect.value;
                    currentOrderBeingEdited.status = newStatus;
                    alert(`Status pesanan ${currentOrderBeingEdited.id} berhasil diubah menjadi ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}.`);
                    applyFilters(); // Render ulang tabel setelah perubahan status
                    closeStatusModal(); // Tutup modal
                }
            });


            // --- Event Listeners untuk Filter dan Pencarian ---
            filterOrdersBtn.addEventListener('click', applyFilters);
            resetFilterBtn.addEventListener('click', resetFilters);
            orderSearchInput.addEventListener('keyup', (event) => {
                if (event.key === 'Enter') {
                    applyFilters();
                }
            });

            // Initial render: Panggil applyFilters() saat halaman dimuat
            applyFilters();
        });
    </script>
</body>
</html>