const modal = document.getElementById('supplierModal');
const formId = document.getElementById('formId');
const formPerusahaan = document.getElementById('formPerusahaan');
const formKontak = document.getElementById('formKontak');
const formEmail = document.getElementById('formEmail');
const formTelepon = document.getElementById('formTelepon');
const formAlamat = document.getElementById('formAlamat');
const modalTitle = document.getElementById('modalTitle');

function openAddModal() {
    modalTitle.textContent = 'Tambah Pemasok Baru';
    formId.value = '';
    formPerusahaan.value = '';
    formKontak.value = '';
    formEmail.value = '';
    formTelepon.value = '';
    formAlamat.value = '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function openEditModal(data) {
    modalTitle.textContent = 'Edit Pemasok';
    formId.value = data.id;
    formPerusahaan.value = data.perusahaan;
    formKontak.value = data.kontak;
    formEmail.value = data.email;
    formTelepon.value = data.telepon;
    formAlamat.value = data.alamat;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
