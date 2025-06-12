
        // Data pengguna dummy (global agar bisa diakses fungsi lain)
        let usersData = [
            { id: 1, username: 'admin', email: 'admin@example.com', role: 'admin' },
            { id: 2, username: 'pemasok1', email: 'pemasok1@example.com', role: 'pemasok' },
            { id: 3, username: 'staff_gudang', email: 'staff@example.com', role: 'staff' }, // Ini akan difilter
            { id: 4, username: 'pemasok2', email: 'pemasok2@example.com', role: 'pemasok' },
            { id: 5, username: 'staff_logistik', email: 'logistik@example.com', role: 'staff' } // Ini akan difilter
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
        const userSearchInput = document.getElementById('userSearch');
        const roleFilterSelect = document.getElementById('roleFilter');
        let currentEditingId = null;

        // Ambil email admin yang sedang login dari localStorage
        const currentAdminEmail = localStorage.getItem('userEmail');

        // Fungsi untuk menampilkan data pengguna ke tabel
        function renderUsers(filteredData = usersData) {
            userTableBody.innerHTML = '';
            // Filter user yang sedang login (admin itu sendiri)
            const displayedUsers = filteredData.filter(user => user.email !== currentAdminEmail);

            if (displayedUsers.length === 0) {
                userTableBody.innerHTML = `<tr><td colspan="5" class="py-4 px-6 text-center text-gray-500">Tidak ada pengguna lain yang ditemukan.</td></tr>`;
                return;
            }

            displayedUsers.forEach(user => {
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
            userForm.reset();
            userId.value = '';
            currentEditingId = null;
            passwordInput.required = true;
            userModal.style.display = 'flex';
        }

        function openEditModal(id) {
            modalTitle.textContent = 'Edit Pengguna';
            const userToEdit = usersData.find(user => user.id === id);
            if (userToEdit) {
                userId.value = userToEdit.id;
                usernameInput.value = userToEdit.username;
                emailInput.value = userToEdit.email;
                passwordInput.value = '';
                passwordInput.required = false;
                roleInput.value = userToEdit.role;
                currentEditingId = id;
                userModal.style.display = 'flex';
            }
        }

        function closeModal() {
            userModal.style.display = 'none';
        }

        userForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const id = userId.value ? parseInt(userId.value) : null;
            const username = usernameInput.value;
            const email = emailInput.value;
            const password = passwordInput.value;
            const role = roleInput.value;

            if (currentEditingId) {
                const userIndex = usersData.findIndex(u => u.id === id);
                if (userIndex !== -1) {
                    usersData[userIndex].username = username;
                    usersData[userIndex].email = email;
                    if (password) {
                        usersData[userIndex].password = password;
                    }
                    usersData[userIndex].role = role;
                }
            } else {
                if (usersData.some(u => u.email === email)) {
                    alert('Email sudah terdaftar. Gunakan email lain.');
                    return;
                }

                const newId = usersData.length > 0 ? Math.max(...usersData.map(u => u.id)) + 1 : 1;
                usersData.push({
                    id: newId,
                    username: username,
                    email: email,
                    password: password,
                    role: role
                });
            }
            filterUsers();
            closeModal();
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
            renderUsers(filtered);
        }


        // --- Logika Autentikasi dan Render Awal ---
        document.addEventListener('DOMContentLoaded', function() {
            // Perbaikan footer
            document.body.classList.add('flex', 'flex-col', 'min-h-screen');
            document.querySelector('main').classList.add('flex-grow');

            if (localStorage.getItem('userRole') !== 'admin') {
                window.location.href = '../../index.php';
            }
            filterUsers();
        });

        // Fungsi logout client-side (tetap sama)
        function logoutClientSide(event) {
            event.preventDefault();
            localStorage.removeItem('userRole');
            localStorage.removeItem('userEmail');
            window.location.href = '../../logout.php';
        }