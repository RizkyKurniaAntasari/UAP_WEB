// === USER MODAL DOM ELEMENTS ===
const userModal = document.getElementById('userModal');
const modalTitle = document.getElementById('modalTitle');
const userId = document.getElementById('userId');

const namaInput = document.getElementById('nama');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const roleSelect = document.getElementById('role');

// === TAMBAH USER ===
function openAddModal() {
    modalTitle.textContent = 'Tambah Pengguna Baru';
    document.getElementById('formAction').value = 'tambah'; // ⬅️ INI PENTING!
    userId.value = '';

    namaInput.value = '';
    emailInput.value = '';
    passwordInput.value = '';
    roleSelect.selectedIndex = 0;

    userModal.classList.remove('hidden');
    userModal.classList.add('flex');
}

// === EDIT USER ===
function openEditModal(data) {
    modalTitle.textContent = 'Edit Pengguna';
    document.getElementById('formAction').value = 'edit'; // 🛠️ PENTING BANGET
    userId.value = data.id;
    namaInput.value = data.nama;
    emailInput.value = data.email;
    passwordInput.value = ''; // Kosongkan password
    roleSelect.value = data.role;

    userModal.classList.remove('hidden');
    userModal.classList.add('flex');
}

// === TUTUP MODAL ===
function closeModal() {
    userModal.classList.add('hidden');
    userModal.classList.remove('flex');
}
