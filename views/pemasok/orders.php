<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Pemasok Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans flex flex-col min-h-screen">

    <nav class="bg-green-700 p-4 shadow-md text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="dashboard.php" class="text-2xl font-bold">Pemasok Dashboard</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:text-green-200">Beranda</a>
                <a href="my_products.php" class="hover:text-green-200">Produk Saya</a>
                <a href="orders.php" class="hover:text-green-200 font-semibold">Pesanan</a> <a href="../../logout.php" class="bg-red-600 px-3 py-1 rounded-md hover:bg-red-700 transition duration-300" onclick="logoutClientSide(event)">Logout</a>
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
                    <button class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300">Filter Pesanan</button>
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

            <div class="flex justify-center mt-6 space-x-2">
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">Previous</a>
                <a href="#" class="px-4 py-2 border border-green-500 bg-green-500 text-white rounded-md">1</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">2</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">Next</a>
            </div>

        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 text-center mt-8">
        <div class="container mx-auto px-6">
            <p class="text-sm">&copy; 2025 Sistem Inventory. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script>
        // Logika autentikasi sisi klien
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('userRole') !== 'pemasok') {
                window.location.href = '../../index.php'; // Kembali ke index.php di root
            }

            // Data pesanan dummy (hanya pesanan yang melibatkan produk dari 'pemasok@example.com')
            const allOrdersData = [
                { id: 'ORD001', date: '2025-06-10', product: 'Kemeja Pria Casual', quantity: 2, price_per_unit: 120000, status: 'pending', supplierEmail: 'pemasok@example.com' },
                { id: 'ORD002', date: '2025-06-09', product: 'Laptop Gaming X1', quantity: 1, price_per_unit: 15000000, status: 'diproses', supplierEmail: 'ptabclobal@example.com' },
                { id: 'ORD003', date: '2025-06-08', product: 'Celana Jeans Slim Fit', quantity: 3, price_per_unit: 250000, status: 'dikirim', supplierEmail: 'pemasok@example.com' },
                { id: 'ORD004', date: '2025-06-07', product: 'Berliner Coklat', quantity: 10, price_per_unit: 15000, status: 'selesai', supplierEmail: 'umkmdonut@example.com' },
                { id: 'ORD005', date: '2025-06-06', product: 'Kemeja Pria Casual', quantity: 1, price_per_unit: 120000, status: 'dibatalkan', supplierEmail: 'pemasok@example.com' },
            ];

            const currentUserEmail = localStorage.getItem('userEmail'); // Ambil email pemasok yang login
            // Filter pesanan yang supplierEmail-nya cocok dengan email pemasok yang sedang login
            const myOrdersData = allOrdersData.filter(order => order.supplierEmail === currentUserEmail);

            const orderTableBody = document.getElementById('orderTableBody');

            // Fungsi untuk menampilkan data pesanan ke tabel
            function renderOrders() {
                orderTableBody.innerHTML = ''; // Bersihkan tabel sebelum render ulang
                if (myOrdersData.length === 0) {
                    orderTableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="py-4 px-6 text-center text-gray-500">Belum ada pesanan yang melibatkan produk Anda.</td>
                        </tr>
                    `;
                    return;
                }

                myOrdersData.forEach(order => {
                    const total_price = order.quantity * order.price_per_unit;
                    let statusColorClass = '';
                    switch(order.status) {
                        case 'pending': statusColorClass = 'bg-yellow-200 text-yellow-800'; break;
                        case 'diproses': statusColorClass = 'bg-blue-200 text-blue-800'; break;
                        case 'dikirim': statusColorClass = 'bg-purple-200 text-purple-800'; break;
                        case 'selesai': statusColorClass = 'bg-green-200 text-green-800'; break;
                        case 'dibatalkan': statusColorClass = 'bg-red-200 text-red-800'; break;
                    }

                    const row = `
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">${order.id}</td>
                            <td class="py-3 px-6 text-left">${order.date}</td>
                            <td class="py-3 px-6 text-left">${order.product}</td>
                            <td class="py-3 px-6 text-center">${order.quantity}</td>
                            <td class="py-3 px-6 text-right">Rp ${total_price.toLocaleString('id-ID')}</td>
                            <td class="py-3 px-6 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold ${statusColorClass}">
                                    ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center space-x-2">
                                    <button class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Lihat Detail">
                                        👁️
                                    </button>
                                    <button class="w-6 h-6 transform hover:text-green-500 hover:scale-110" title="Ubah Status">
                                        🔄
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    orderTableBody.innerHTML += row;
                });
            }

            renderOrders(); // Panggil fungsi untuk menampilkan data saat halaman dimuat
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