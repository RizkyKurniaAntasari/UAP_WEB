    <nav class="bg-blue-800 p-4 shadow-md text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="dashboard.php" class="text-2xl font-bold">Admin Dashboard</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:text-blue-200">Beranda</a>
                <a href="products.php" class="hover:text-blue-200">Barang</a>
                <a href="categories.php" class="hover:text-blue-200">Kategori</a>
                <a href="suppliers.php" class="hover:text-blue-200">Pemasok</a>
                <a href="transactions.php" class="hover:text-blue-200">Transaksi</a>
                <a href="users.php" class="hover:text-blue-200">Pengguna</a>
                <a href="../../logout.php" class="bg-red-600 px-3 py-1 rounded-md hover:bg-red-700 transition duration-300" onclick="logoutClientSide(event)">Logout</a>
            </div>
        </div>
    </nav>