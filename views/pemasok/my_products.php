<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Saya - Pemasok Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans flex flex-col min-h-screen"> <nav class="bg-green-700 p-4 shadow-md text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="dashboard.php" class="text-2xl font-bold">Pemasok Dashboard</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:text-green-200">Beranda</a>
                <a href="my_products.php" class="hover:text-green-200 font-semibold">Produk Saya</a> <a href="orders.php" class="hover:text-green-200">Pesanan</a>
                <a href="../../logout.php" class="bg-red-600 px-3 py-1 rounded-md hover:bg-red-700 transition duration-300" onclick="logoutClientSide(event)">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-8 flex-grow"> <h1 class="text-4xl font-bold text-gray-800 mb-6">Produk yang Saya Sediakan</h1>
        <p class="text-gray-700 mb-8">Daftar barang yang Anda sediakan untuk sistem inventaris.</p>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Daftar Produk</h2>
                <a href="#" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-300">Ajukan Produk Baru</a>
            </div>

            <div class="mb-4">
                <input type="text" placeholder="Cari nama produk..." class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 w-full md:w-1/3">
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

            <div class="flex justify-center mt-6 space-x-2">
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">Previous</a>
                <a href="#" class="px-4 py-2 border border-green-500 bg-green-500 text-white rounded-md">1</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">2</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">Next</a>
            </div>

        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 text-center mt-8"> <div class="container mx-auto px-6">
            <p class="text-sm">&copy; 2025 Sistem Inventory. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script>
        // Logika autentikasi sisi klien
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('userRole') !== 'pemasok') {
                window.location.href = '../../index.php'; // Kembali ke index.php di root
            }

            // Data produk dummy (hanya produk yang diasosiasikan dengan 'pemasok@example.com')
            const allProductsData = [
                { id: 1, name: 'Laptop Gaming X1', category: 'Elektronik', supplierEmail: 'ptabclobal@example.com', stock: 15, price: 15000000 },
                { id: 2, name: 'Kemeja Pria Casual', category: 'Pakaian', supplierEmail: 'pemasok@example.com', stock: 200, price: 120000 },
                { id: 3, name: 'Berliner Coklat', category: 'Makanan', supplierEmail: 'umkmdonut@example.com', stock: 50, price: 15000 },
                { id: 4, name: 'Mouse Wireless A10', category: 'Elektronik', supplierEmail: 'ptabclobal@example.com', stock: 75, price: 180000 },
                { id: 5, name: 'Celana Jeans Slim Fit', category: 'Pakaian', supplierEmail: 'pemasok@example.com', stock: 150, price: 250000 },
                { id: 6, name: 'Air Mineral 600ml', category: 'Minuman', supplierEmail: 'pttirta@example.com', stock: 300, price: 3000 },
            ];

            const currentUserEmail = localStorage.getItem('userEmail'); // Ambil email pemasok yang login
            const myProductsData = allProductsData.filter(product => product.supplierEmail === currentUserEmail);

            const productTableBody = document.getElementById('productTableBody');

            // Fungsi untuk menampilkan data produk ke tabel
            function renderProducts() {
                productTableBody.innerHTML = ''; // Bersihkan tabel sebelum render ulang
                if (myProductsData.length === 0) {
                    productTableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="py-4 px-6 text-center text-gray-500">Belum ada produk yang Anda sediakan.</td>
                        </tr>
                    `;
                    return;
                }

                myProductsData.forEach(product => {
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
                            <td class="py-3 px-6 text-right">Rp ${product.price.toLocaleString('id-ID')}</td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center space-x-2">
                                    <button class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Edit">
                                        ✏️
                                    </button>
                                    <button class="w-6 h-6 transform hover:text-red-500 hover:scale-110" title="Hapus">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    productTableBody.innerHTML += row;
                });
            }

            renderProducts(); // Panggil fungsi untuk menampilkan data saat halaman dimuat
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