<?php
// session_start();
include_once __DIR__ . '/../../src/db.php';
include_once __DIR__ . '/../../src/functions.php';

// === Tambah / Edit User ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['tambah', 'edit'])) {
    $id       = $_POST['id'] ?? '';
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'];

    // Validasi sederhana (boleh ditambah sesuai kebutuhan)
    if (empty($nama) || empty($email) || empty($role)) {
        die('Semua field wajib diisi');
    }

    // Jika edit
    if ($_POST['action'] === 'edit' && $id) {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $query = "UPDATE users SET nama='$nama', email='$email', password='$hashedPassword', role='$role' WHERE id='$id'";
        } else {
            $query = "UPDATE users SET nama='$nama', email='$email', role='$role' WHERE id='$id'";
        }
    }
    // Jika tambah
    else {
        if (empty($password)) {
            die('Password wajib diisi untuk pengguna baru');
        }
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hashedPassword', '$role')";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        die("Gagal menyimpan data pengguna: " . mysqli_error($conn));
    }
}

// === Hapus User ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    $id = $_POST['hapus_id'];
    $query = "DELETE FROM users WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        die("Gagal menghapus pengguna: " . mysqli_error($conn));
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="bg-gray-100 font-sans flex flex-col min-h-screen">

    <?php include_once 'components/navbar.php' ?>

    <main class="container mx-auto px-6 py-8 flex-grow">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Manajemen Pengguna</h1>
        <p class="text-gray-700 mb-8">Kelola akun pengguna yang memiliki akses ke sistem inventaris.</p>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Daftar Pengguna</h2>
                <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300" onclick="openAddModal()">Tambah Pengguna Baru</a>
            </div>

            <div class="mb-4 flex items-center space-x-4">
                <input type="text" id="userSearch" placeholder="Cari pengguna..." class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">
                <select id="roleFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="pemasok">Pemasok</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Nama Pengguna</th>
                            <th class="py-3 px-6 text-left">Email</th>
                            <th class="py-3 px-6 text-left">Role</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light" id="userTableBody">
                        <?php
                        // Ambil data pengguna dari database
                        $query = "SELECT * FROM users";
                        $users = mysqli_query($conn, $query);

                        $currentAdminEmail = $_SESSION['user']['email'] ?? ''; // pastikan sudah login

                        foreach ($users as $user):
                            // Lewati admin yang sedang login
                            if ($user['email'] === $currentAdminEmail) continue;

                            // Tentukan kelas warna untuk role
                            $roleColor = match ($user['role']) {
                                'admin'   => 'bg-red-200 text-red-800',
                                'pemasok' => 'bg-green-200 text-green-800',
                                default   => 'bg-gray-200 text-gray-800',
                            };
                        ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left"><?= htmlspecialchars($user['id']) ?></td>
                                <td class="py-3 px-6 text-left"><?= htmlspecialchars($user['nama']) ?></td>
                                <td class="py-3 px-6 text-left"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="py-3 px-6 text-left">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $roleColor ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex item-center justify-center space-x-2">
                                        <button class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Edit" onclick="openEditModal(<?= htmlspecialchars(json_encode($user)) ?>)">
                                            ✏️
                                        </button>
                                        <form method="POST" action="" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');" style="display:inline;">
                                            <input type="hidden" name="hapus_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="w-6 h-6 transform hover:text-red-500 hover:scale-110" title="Hapus">🗑️</button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </main>

    <?php include_once 'components/footer.php' ?>

    <div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden justify-center items-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md mx-4 relative">
            <span class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl cursor-pointer" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800 mb-6">Tambah Pengguna Baru</h2>
            <form id="userForm" method="POST" action="">
                <input type="hidden" name="id" id="userId">
                <input type="hidden" name="action" id="formAction" value="tambah">
                <div class="mb-4">
                    <label for="nama" class="block text-gray-700 text-sm font-semibold mb-2">Nama Pengguna</label>
                    <input type="text" id="nama" name="nama" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password saat edit.</p>
                </div>
                <div class="mb-6">
                    <label for="role" class="block text-gray-700 text-sm font-semibold mb-2">Role</label>
                    <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="admin">Admin</option>
                        <option value="pemasok">Pemasok</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-800 px-5 py-2 rounded-md hover:bg-gray-400 transition duration-300">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition duration-300">Simpan</button>
                </div>
            </form>

        </div>
    </div>

    <script src="js/user.js"></script>
</body>

</html>