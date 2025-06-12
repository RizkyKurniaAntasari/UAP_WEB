const barangModal = document.getElementById('barangModal');
const barangModalTitle = document.getElementById('barangModalTitle');
const barangFormId = document.getElementById('barangFormId');
const formNamaBarang = document.getElementById('formNamaBarang');
const formKategori = document.getElementById('formKategori');
const formPemasok = document.getElementById('formPemasok');
const formStok = document.getElementById('formStok');
const formHarga = document.getElementById('formHarga');

function openAddBarangModal() {
    barangModalTitle.textContent = 'Tambah Barang Baru';
    barangFormId.value = '';
    formNamaBarang.value = '';
    formKategori.selectedIndex = 0;
    formPemasok.selectedIndex = 0;
    formStok.value = '';
    formHarga.value = '';
    barangModal.classList.remove('hidden');
    barangModal.classList.add('flex');
}

function closeBarangModal() {
    barangModal.classList.add('hidden');
    barangModal.classList.remove('flex');
}
