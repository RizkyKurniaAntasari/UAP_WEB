<?php
include_once __DIR__ . '/../../src/db.php';
$pemasok = $conn->query("SELECT * FROM pemasok ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Manajemen Pemasok</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="bg-gray-100 font-sans flex min-h-screen flex-col">
    <?php include_once 'components/navbar.php' ?>

    <main class="container mx-auto px-6 py-8 flex-grow">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Manajemen Pemasok</h1>
        <p class="text-gray-700 mb-8">Kelola pemasok barang.</p>

        <div class="bg-white p-6 rounded shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold">Daftar Pemasok</h2>
                <button onclick="openAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah Pemasok Baru</button>
            </div>

            <div class="mb-4">
                <input type="text" id="suppliersSearch" placeholder="Cari yang kamu butuhkan" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">
            </div>

            <table class="min-w-full table-auto border">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Nama Perusahaan</th>
                        <th class="px-4 py-2">Nama Kontak</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Telepon</th>
                        <th class="px-4 py-2">Alamat</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 text-center">
                    <?php while ($row = $pemasok->fetch_assoc()) : ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?= $row['id'] ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['perusahaan']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['kontak']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['telepon']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['alamat']) ?></td>
                            <td class="px-4 py-2 flex gap-2 justify-center">
                                <button onclick='openEditModal(<?= json_encode($row) ?>)' class="text-blue-600 hover:underline">Edit</button>
                                <a href="../../controllers/admin/suppliers.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus data ini?')" class="text-red-600 hover:underline">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include_once 'components/footer.php' ?>

    <div id="supplierModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-md shadow-lg w-full max-w-md">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Pemasok Baru</h2>
            <form method="POST" action="../../controllers/admin/suppliers.php" class="space-y-4">
                <input type="hidden" name="id" id="formId">
                <div>
                    <label for="formPerusahaan" class="block text-sm font-medium">Nama Perusahaan</label>
                    <input type="text" id="formPerusahaan" name="perusahaan" class="w-full border px-3 py-2 rounded" required>
                </div>
                <div>
                    <label for="formKontak" class="block text-sm font-medium">Kontak Person</label>
                    <input type="text" id="formKontak" name="kontak" class="w-full border px-3 py-2 rounded" required>
                </div>
                <div>
                    <label for="formEmail" class="block text-sm font-medium">Email</label>
                    <input type="email" id="formEmail" name="email" class="w-full border px-3 py-2 rounded" required>
                </div>
                <div>
                    <label for="formTelepon" class="block text-sm font-medium">Telepon</label>
                    <input type="text" id="formTelepon" name="telepon" class="w-full border px-3 py-2 rounded" required>
                </div>
                <div>
                    <label for="formAlamat" class="block text-sm font-medium">Alamat</label>
                    <textarea id="formAlamat" name="alamat" rows="3" class="w-full border px-3 py-2 rounded"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/suppliers.js"></script>
</body>

</html>