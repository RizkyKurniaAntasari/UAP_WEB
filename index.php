<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Sistem Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <nav class="bg-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-blue-600">Sistem Inventaris</a>
            <div>
                <a href="index.php" class="text-gray-700 hover:text-blue-500 mx-2">Beranda</a>
                <a href="login.php" class="text-gray-700 hover:text-blue-500 mx-2">Login</a>
                <a href="register.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">Daftar</a>
            </div>
        </div>
    </nav>

    <header class="bg-blue-600 text-white py-20 text-center">
        <div class="container mx-auto px-6">
            <h1 class="text-5xl font-extrabold mb-4">Kelola Inventaris Anda dengan Mudah!</h1>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                Sistem inventaris sederhana untuk membantu Anda mencatat dan mengelola barang di gudang.
            </p>
            <div class="space-x-4">
                <a href="#fitur" class="bg-white text-blue-600 px-6 py-3 rounded-full font-semibold hover:bg-gray-200 transition duration-300">Pelajari Fitur</a>
                <a href="register.php" class="bg-green-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-green-700 transition duration-300">Daftar Gratis</a>
            </div>
        </div>
    </header>

    <section id="fitur" class="py-16 bg-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-12">Mengapa Memilih Kami?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <div class="bg-gray-50 p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <div class="text-5xl text-blue-500 mb-4">📦</div>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-2">Manajemen Barang Komprehensif</h3>
                    <p class="text-gray-600">Catat detail barang, kategori, dan pemasok dengan mudah.</p>
                </div>
                <div class="bg-gray-50 p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <div class="text-5xl text-green-500 mb-4">📈</div>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-2">Stok Otomatis & Akurat</h3>
                    <p class="text-gray-600">Stok diperbarui otomatis setiap ada transaksi masuk/keluar.</p>
                </div>
                <div class="bg-gray-50 p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <div class="text-5xl text-purple-500 mb-4">📊</div>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-2">Laporan Transaksi Mudah</h3>
                    <p class="text-gray-600">Pantau pergerakan barang dengan laporan masuk dan keluar.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-blue-500 text-white text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold mb-4">Siap Mengatur Inventaris Anda?</h2>
            <p class="text-lg mb-8">Daftar sekarang dan rasakan kemudahan mengelola stok barang Anda.</p>
            <a href="register.php" class="bg-green-600 text-white px-8 py-4 rounded-full font-semibold text-lg hover:bg-green-700 transition duration-300">Mulai Sekarang!</a>
        </div>
    </section>

    <footer class="bg-gray-800 text-white py-4 text-center">
        <div class="container mx-auto px-6">
            <p class="text-sm">&copy; 2025 Sistem Inventaris. Hak Cipta Dilindungi.</p>
            <div class="mt-2 space-x-4 text-sm">
                <a href="#" class="hover:text-blue-400">Kebijakan Privasi</a>
                <a href="#" class="hover:text-blue-400">Syarat & Ketentuan</a>
            </div>
        </div>
    </footer>

</body>
</html>