const productModal = document.getElementById('productModal');
const productModalTitle = document.getElementById('productModalTitle');
// Menggunakan productFormAction untuk mengatur nilai input hidden 'action'
const productFormAction = document.getElementById('productFormAction'); 
const productId = document.getElementById('productId'); 

// Mereferensikan input form di modal
const formNamaBarang = document.getElementById('formNamaBarang');
const formKategori = document.getElementById('formKategori');
const formPemasok = document.getElementById('formPemasok'); // Sudah ada di HTML modal yang baru
const formStok = document.getElementById('formStok');
const stokFieldWrapper = document.getElementById('stokFieldWrapper'); // Untuk menyembunyikan/menampilkan field stok
const formHargaBeli = document.getElementById('formHargaBeli'); 
const formHargaJual = document.getElementById('formHargaJual'); 

// Fungsi untuk membuka modal Tambah Barang
function openAddProductModal() {
    productModalTitle.textContent = 'Tambah Barang Baru';
    productFormAction.value = 'add_barang'; // Set action untuk operasi tambah
    productId.value = ''; // Kosongkan ID karena ini barang baru
    
    // Reset nilai input form
    formNamaBarang.value = '';
    formKategori.selectedIndex = 0; // Pilih opsi pertama (Pilih Kategori)
    formPemasok.selectedIndex = 0; // Pilih opsi pertama (Pilih Pemasok)
    formStok.value = ''; // Stok awal biasanya 0 dan akan diisi via transaksi
    formHargaBeli.value = 0;
    formHargaJual.value = 0;
    
    // Sembunyikan field stok saat menambah barang baru
    stokFieldWrapper.classList.add('hidden');
    stokFieldWrapper.classList.remove('hidden');
    
    productModal.classList.remove('hidden');
    productModal.classList.add('flex'); // Gunakan flex untuk centering
}

// Fungsi untuk membuka modal Edit Barang
function openEditProductModal(data) {
    productModalTitle.textContent = 'Edit Barang';
    // Change 'update_barang' to 'edit_barang' to match the PHP controller
    productFormAction.value = 'edit_barang'; // Corrected action for update operation

    productId.value = data.id;

    // Isi form dengan data barang yang akan diedit
    formNamaBarang.value = data.nama_barang;
    formKategori.value = data.id_kategori; // Memilih opsi kategori yang sesuai
    formPemasok.value = data.id_pemasok || ''; // Memilih opsi pemasok, fallback jika null
    formStok.value = data.stok;
    formHargaBeli.value = parseFloat(data.harga_beli).toFixed(2); // Pastikan format 2 desimal
    formHargaJual.value = parseFloat(data.harga_jual).toFixed(2); // Pastikan format 2 desimal

    // Tampilkan field stok saat mengedit barang
    stokFieldWrapper.classList.remove('hidden');

    productModal.classList.remove('hidden');
    productModal.classList.add('flex'); // Gunakan flex untuk centering
}

// Fungsi untuk menutup modal Barang
function closeProductModal() {
    productModal.classList.add('hidden');
    productModal.classList.remove('flex');
}

// Fungsi konfirmasi hapus (biasanya sudah ada di index.php utama, tapi tidak ada salahnya ada di sini juga)
function confirmDelete() {
    return confirm('Apakah Anda yakin ingin menghapus data ini? Aksi ini tidak dapat dibatalkan.');
}