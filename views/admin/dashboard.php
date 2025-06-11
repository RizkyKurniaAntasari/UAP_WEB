<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg">
            <div class="p-6 font-bold text-lg border-b border-gray-200">Admin Pemasok</div>
            <nav class="mt-6">
                <ul class="space-y-2">
                    <li><a href="#" class="block px-6 py-2 hover:bg-gray-200">Dashboard</a></li>
                    <li><a href="#" class="block px-6 py-2 hover:bg-gray-200">Data Barang</a></li>
                    <li><a href="#" class="block px-6 py-2 hover:bg-gray-200">Pemasok</a></li>
                    <li><a href="#" class="block px-6 py-2 hover:bg-gray-200">Permintaan</a></li>
                    <li><a href="#" class="block px-6 py-2 hover:bg-gray-200">Laporan</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <h1 class="text-2xl font-semibold mb-4">Dashboard Pemasok</h1>

            <!-- Statistik Kartu -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 shadow rounded-lg">
                    <p class="text-sm text-gray-500">Total Barang</p>
                    <p class="text-2xl font-bold">124</p>
                </div>
                <div class="bg-white p-4 shadow rounded-lg">
                    <p class="text-sm text-gray-500">Pemasok Terdaftar</p>
                    <p class="text-2xl font-bold">18</p>
                </div>
                <div class="bg-white p-4 shadow rounded-lg">
                    <p class="text-sm text-gray-500">Stok Habis</p>
                    <p class="text-2xl font-bold text-red-600">5</p>
                </div>
                <div class="bg-white p-4 shadow rounded-lg">
                    <p class="text-sm text-gray-500">Permintaan Baru</p>
                    <p class="text-2xl font-bold text-blue-600">9</p>
                </div>
            </div>

            <!-- Tabel Barang -->
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-4">Barang Terbaru</h2>
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2">Nama Barang</th>
                            <th class="px-4 py-2">Kategori</th>
                            <th class="px-4 py-2">Stok</th>
                            <th class="px-4 py-2">Tanggal Masuk</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t">
                            <td class="px-4 py-2">Beras Medium</td>
                            <td class="px-4 py-2">Sembako</td>
                            <td class="px-4 py-2">35</td>
                            <td class="px-4 py-2">10 Juni 2025</td>
                        </tr>
                        <tr class="border-t">
                            <td class="px-4 py-2">Minyak Goreng</td>
                            <td class="px-4 py-2">Sembako</td>
                            <td class="px-4 py-2 text-red-600">0</td>
                            <td class="px-4 py-2">9 Juni 2025</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>

</html>