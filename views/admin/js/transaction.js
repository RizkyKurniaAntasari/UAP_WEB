const transactionModal = document.getElementById('transactionModal');
const modalTitle = document.getElementById('modalTitle');
const transactionForm = document.getElementById('transactionForm');

// Form elements
const transactionId = document.getElementById('transactionId');
const transactionBarangId = document.getElementById('transactionBarangId');
const transactionJenis = document.getElementById('transactionJenis');
const transactionKuantitas = document.getElementById('transactionKuantitas');
const transactionPemasokId = document.getElementById('transactionPemasokId');
const transactionCatatan = document.getElementById('transactionCatatan');

// Tombol tambah transaksi
document.getElementById('addTransactionBtn').addEventListener('click', () => {
    openTransactionModal();
});

// Buka modal untuk transaksi baru
function openTransactionModal() {
    modalTitle.textContent = 'Tambah Transaksi Baru';
    transactionForm.reset();
    transactionId.value = '';
    transactionModal.classList.remove('hidden');
    transactionModal.classList.add('flex');
}

// Tutup modal
function closeTransactionModal() {
    transactionModal.classList.add('hidden');
    transactionModal.classList.remove('flex');
}

// Submit form
transactionForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const dataTransaksi = {
        id: transactionId.value,
        barang_id: transactionBarangId.value,
        jenis: transactionJenis.value,
        kuantitas: transactionKuantitas.value,
        pemasok_id: transactionPemasokId.value,
        catatan: transactionCatatan.value
    };

    console.log('Data transaksi disubmit:', dataTransaksi);

    // Simulasi request backend
    // fetch(...)

    closeTransactionModal();
    transactionForm.reset();
});
