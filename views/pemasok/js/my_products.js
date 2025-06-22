
// Hapus fungsi logoutClientSide(event) jika tidak digunakan lagi
// Cukup arahkan langsung ke '../../logout.php'

// --- DOM Elements ---
const addProductBtn = document.getElementById('addProductBtn');
const productModal = document.getElementById('productModal');
const cancelModalBtnAdd = document.getElementById('cancelModalBtnAdd'); // Tombol batal untuk modal tambah

const editProdukModal = document.getElementById('editProdukModal'); // Modal edit
const cancelModalBtnEdit = document.getElementById('cancelModalBtnEdit'); // Tombol batal untuk modal edit
const flashMessage = document.getElementById('flashMessage');

// --- Fungsi untuk Modal Tambah Produk ---
addProductBtn.addEventListener('click', () => {
    productModal.classList.remove('hidden');
    productModal.classList.add('flex');
    // Reset form jika perlu
    document.getElementById('productNameAdd').value = '';
    document.getElementById('productStockAdd').value = '';
    document.getElementById('satuanAdd').value = '';
    document.getElementById('hargaBeliAdd').value = '';
    document.getElementById('hargaJualAdd').value = '';
    document.getElementById('productDescAdd').value = '';
    document.getElementById('productCategoryAdd').selectedIndex = 0; // Pilih opsi pertama
});

cancelModalBtnAdd.addEventListener('click', () => {
    productModal.classList.remove('flex');
    productModal.classList.add('hidden');
});

// --- Fungsi untuk Modal Edit Produk ---
// Ini akan dipicu oleh PHP saat halaman dimuat jika ada parameter GET untuk edit
// Karena modal edit ditampilkan berdasarkan kondisi PHP ($isEditMode),
// kita hanya perlu menangani tombol batalnya di JavaScript.
cancelModalBtnEdit.addEventListener('click', () => {
    editProdukModal.classList.remove('flex');
    editProdukModal.classList.add('hidden');
    // Penting: Hapus parameter GET dari URL setelah membatalkan edit
    window.history.replaceState({}, document.title, window.location.pathname);
});

// Tangani flash message (optional: bisa ditangani sepenuhnya di PHP juga)
// Jika Anda ingin flash message hilang setelah beberapa detik tanpa refresh,
// gunakan setTimeout di sini.
window.onload = function () {
    if (flashMessage && !flashMessage.classList.contains('hidden')) {
        setTimeout(() => {
            flashMessage.classList.add('hidden');
            // Hapus parameter GET 'status' dari URL setelah pesan ditampilkan
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 5000); // Pesan hilang setelah 5 detik
    }
};

