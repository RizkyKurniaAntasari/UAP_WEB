<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Style untuk modal/popup */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 90%;
            max-width: 500px;
            position: relative;
        }
        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
        }
        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans flex flex-col min-h-screen"> <nav class="bg-blue-800 p-4 shadow-md text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="dashboard.php" class="text-2xl font-bold">Admin Dashboard</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:text-blue-200">Beranda</a>
                <a href="products.php" class="hover:text-blue-200">Barang</a>
                <a href="categories.php" class="hover:text-blue-200">Kategori</a>
                <a href="suppliers.php" class="hover:text-blue-200">Pemasok</a>
                <a href="transactions.php" class="hover:text-blue-200">Transaksi</a>
                <a href="users.php" class="hover:text-blue-200 font-semibold">Pengguna</a> <a href="../../logout.php" class="bg-red-600 px-3 py-1 rounded-md hover:bg-red-700 transition duration-300" onclick="logoutClientSide(event)">Logout</a>
            </div>
        </div>
    </nav>

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
                <select id="roleFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="filterUsers()">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="pemasok">Pemasok</option>
                    <option value="staff">Staff</option>
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
                        </tbody>
                </table>
            </div>

            <div class="flex justify-center mt-6 space-x-2">
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">Previous</a>
                <a href="#" class="px-4 py-2 border border-blue-500 bg-blue-500 text-white rounded-md">1</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">2</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-200">Next</a>
            </div>

        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 text-center mt-8">
        <div class="container mx-auto px-6">
            <p class="text-sm">&copy; 2025 Sistem Inventaris. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <div id="userModal" class="modal hidden">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800 mb-6">Tambah Pengguna Baru</h2>
            <form id="userForm">
                <input type="hidden" id="userId">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Nama Pengguna</label>
                    <input type="text" id="username" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                    <input type="password" id="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password saat edit.</p>
                </div>
                <div class="mb-6">
                    <label for="role" class="block text-gray-700 text-sm font-semibold mb-2">Role</label>
                    <select id="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="admin">Admin</option>
                        <option value="pemasok">Pemasok</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-800 px-5 py-2 rounded-md hover:bg-gray-400 transition duration-300">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition duration-300">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Data pengguna dummy (global agar bisa diakses fungsi lain)
        let usersData = [
            { id: 1, username: 'admin', email: 'admin@example.com', role: 'admin' },
            { id: 2, username: 'pemasok1', email: 'pemasok1@example.com', role: 'pemasok' },
            { id: 3, username: 'staff_gudang', email: 'staff@example.com', role: 'staff' },
            { id: 4, username: 'pemasok2', email: 'pemasok2@example.com', role: 'pemasok' },
            { id: 5, username: 'staff_logistik', email: 'logistik@example.com', role: 'staff' }
        ];

        const userTableBody = document.getElementById('userTableBody');
        const userModal = document.getElementById('userModal');
        const modalTitle = document.getElementById('modalTitle');
        const userForm = document.getElementById('userForm');
        const userId = document.getElementById('userId');
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const roleInput = document.getElementById('role');
        const userSearchInput = document.getElementById('userSearch'); // Untuk pencarian
        const roleFilterSelect = document.getElementById('roleFilter'); // Untuk filter role
        let currentEditingId = null; // Untuk melacak pengguna yang sedang diedit

        // Fungsi untuk menampilkan data pengguna ke tabel
        function renderUsers(filteredData = usersData) {
            userTableBody.innerHTML = ''; // Bersihkan tabel sebelum render ulang
            if (filteredData.length === 0) {
                userTableBody.innerHTML = `<tr><td colspan="5" class="py-4 px-6 text-center text-gray-500">Tidak ada pengguna yang ditemukan.</td></tr>`;
                return;
            }

            filteredData.forEach(user => {
                const row = `
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">${user.id}</td>
                        <td class="py-3 px-6 text-left">${user.username}</td>
                        <td class="py-3 px-6 text-left">${user.email}</td>
                        <td class="py-3 px-6 text-left">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                ${user.role === 'admin' ? 'bg-red-200 text-red-800' : ''}
                                ${user.role === 'pemasok' ? 'bg-green-200 text-green-800' : ''}
                                ${user.role === 'staff' ? 'bg-blue-200 text-blue-800' : ''}
                            ">${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <button class="w-6 h-6 transform hover:text-blue-500 hover:scale-110" title="Edit" onclick="openEditModal(${user.id})">
                                    ✏️
                                </button>
                                <button class="w-6 h-6 transform hover:text-red-500 hover:scale-110" title="Hapus" onclick="deleteUser(${user.id})">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                userTableBody.innerHTML += row;
            });
        }

        // --- Fungsionalitas Hapus (Delete) ---
        function deleteUser(id) {
            if (confirm(`Apakah Anda yakin ingin menghapus pengguna dengan ID ${id}?`)) {
                usersData = usersData.filter(user => user.id !== id);
                filterUsers(); // Render ulang dengan filter yang aktif
            }
        }

        // --- Fungsionalitas Tambah (Create) & Edit (Update) via Modal ---
        function openAddModal() {
            modalTitle.textContent = 'Tambah Pengguna Baru';
            userForm.reset(); // Kosongkan formulir
            userId.value = ''; // Pastikan ID kosong untuk mode tambah
            currentEditingId = null;
            passwordInput.required = true; // Password wajib diisi saat tambah
            userModal.style.display = 'flex'; // Tampilkan modal
        }

        function openEditModal(id) {
            modalTitle.textContent = 'Edit Pengguna';
            const userToEdit = usersData.find(user => user.id === id);
            if (userToEdit) {
                userId.value = userToEdit.id;
                usernameInput.value = userToEdit.username;
                emailInput.value = userToEdit.email;
                passwordInput.value = ''; // Kosongkan password saat edit (tidak menampilkan password lama)
                passwordInput.required = false; // Password tidak wajib diisi saat edit
                roleInput.value = userToEdit.role;
                currentEditingId = id; // Simpan ID pengguna yang sedang diedit
                userModal.style.display = 'flex'; // Tampilkan modal
            }
        }

        function closeModal() {
            userModal.style.display = 'none'; // Sembunyikan modal
        }

        userForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form submit default

            const id = userId.value ? parseInt(userId.value) : null;
            const username = usernameInput.value;
            const email = emailInput.value;
            const password = passwordInput.value; // Ini akan menjadi plain text, TIDAK AMAN untuk produksi
            const role = roleInput.value;

            if (currentEditingId) {
                // Mode Edit (Update)
                const userIndex = usersData.findIndex(u => u.id === id);
                if (userIndex !== -1) {
                    usersData[userIndex].username = username;
                    usersData[userIndex].email = email;
                    if (password) { // Hanya update password jika diisi
                        usersData[userIndex].password = password; // Di sini seharusnya di-hash
                    }
                    usersData[userIndex].role = role;
                }
            } else {
                // Mode Tambah (Create)
                const newId = usersData.length > 0 ? Math.max(...usersData.map(u => u.id)) + 1 : 1;
                usersData.push({
                    id: newId,
                    username: username,
                    email: email,
                    password: password, // Di sini seharusnya di-hash
                    role: role
                });
            }
            filterUsers(); // Render ulang tabel setelah penambahan/pembaruan (dengan filter aktif)
            closeModal(); // Tutup modal
        });

        // --- Fungsionalitas Filter dan Pencarian ---
        userSearchInput.addEventListener('input', filterUsers);
        roleFilterSelect.addEventListener('change', filterUsers);

        function filterUsers() {
            const searchTerm = userSearchInput.value.toLowerCase();
            const selectedRole = roleFilterSelect.value;

            let filtered = usersData.filter(user => {
                const matchesSearch = user.username.toLowerCase().includes(searchTerm) ||
                                      user.email.toLowerCase().includes(searchTerm);
                const matchesRole = selectedRole === '' || user.role === selectedRole;
                return matchesSearch && matchesRole;
            });
            renderUsers(filtered); // Panggil render dengan data yang sudah difilter
        }


        // --- Logika Autentikasi dan Render Awal ---
        document.addEventListener('DOMContentLoaded', function() {
            // Perbaikan footer
            document.body.classList.add('flex', 'flex-col', 'min-h-screen');
            document.querySelector('main').classList.add('flex-grow');

            if (localStorage.getItem('userRole') !== 'admin') {
                window.location.href = '../../index.php'; // Kembali ke index.php di root
            }
            filterUsers(); // Panggil filter untuk menampilkan data awal (dan terapkan filter jika sudah ada)
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