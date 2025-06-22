// js/transaction.js

document.addEventListener('DOMContentLoaded', function() {
    // --- Elemen DOM yang Dibutuhkan ---
    const transactionModal = document.getElementById('transactionModal');
    const addTransactionBtn = document.getElementById('addTransactionBtn');
    const transactionForm = document.getElementById('transactionForm');
    const transactionBarangId = document.getElementById('transactionBarangId');
    const transactionJenis = document.getElementById('transactionJenis');
    const transactionKuantitas = document.getElementById('transactionKuantitas');
    const currentStockHiddenInput = document.getElementById('currentStock'); // Ini adalah input hidden untuk menyimpan stok saat ini
    const predictedStock = document.getElementById('predictedStock'); // Input untuk menampilkan stok setelah transaksi
    const pemasokField = document.getElementById('pemasokField');
    const transactionPemasokId = document.getElementById('transactionPemasokId');
    const transactionTableBody = document.getElementById('transactionTableBody'); // Untuk tabel transaksi
    const transactionTypeFilter = document.getElementById('transactionTypeFilter');
    const startDateFilter = document.getElementById('startDateFilter');
    const endDateFilter = document.getElementById('endDateFilter');


    // --- Fungsi Inisialisasi Modal Transaksi ---
    // Dipanggil saat modal dibuka untuk mengatur ulang semua field dan stok awal.
    function initializeModal() {
        transactionForm.reset(); // Mengatur ulang semua input form
        document.getElementById('modalTitle').innerText = 'Buat Transaksi Baru';
        document.getElementById('transactionId').value = ''; // Mengosongkan ID transaksi jika ada (untuk mode edit)

        // Mencari opsi barang yang pertama kali memiliki 'value' (bukan placeholder "-- Pilih Barang --")
        const firstActualOption = Array.from(transactionBarangId.options).find(option => option.value !== "");

        if (firstActualOption) {
            // Jika ada barang yang tersedia:
            // 1. Set dropdown barang agar memilih barang pertama tersebut.
            transactionBarangId.value = firstActualOption.value;
            // 2. Ambil nilai stok dari atribut data-stok dan isi ke input hidden 'currentStockHiddenInput'.
            currentStockHiddenInput.value = parseInt(firstActualOption.dataset.stok || '0'); // Menggunakan parseInt dengan fallback '0'
        } else {
            // Jika tidak ada barang sama sekali:
            // 1. Pastikan dropdown memilih opsi placeholder.
            transactionBarangId.value = '';
            // 2. Set 'currentStockHiddenInput' menjadi 0.
            currentStockHiddenInput.value = '0';
        }

        predictedStock.value = '0'; // Stok Prediksi selalu dimulai dari 0
        pemasokField.classList.add('hidden'); // Sembunyikan field pemasok secara default
        transactionPemasokId.removeAttribute('required'); // Pastikan pemasok tidak wajib diisi secara default
        transactionPemasokId.value = ''; // Kosongkan pilihan pemasok

        // Setelah semua nilai diatur, hitung stok prediksi awal
        calculatePredictedStock();
    }

    // --- Event Listener untuk Tombol "Buat Transaksi Baru" ---
    addTransactionBtn.addEventListener('click', function() {
        transactionModal.classList.remove('hidden'); // Tampilkan modal
        transactionModal.classList.add('flex');
        initializeModal(); // Panggil fungsi inisialisasi setiap kali modal dibuka
    });

    // --- Fungsi untuk Menutup Modal Transaksi ---
    window.closeTransactionModal = function() {
        transactionModal.classList.add('hidden'); // Sembunyikan modal
        transactionModal.classList.remove('flex');
    };


    // --- Event Listener untuk Perubahan Pilihan Barang ---
    transactionBarangId.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex]; // Opsi yang baru terpilih
        if (selectedOption && selectedOption.value !== "") {
            // Jika opsi valid (bukan placeholder), ambil stoknya dari data-stok
            const stock = parseInt(selectedOption.dataset.stok || '0');
            currentStockHiddenInput.value = stock; // Update input hidden 'currentStockHiddenInput'
        } else {
            // Jika placeholder dipilih kembali, set 'currentStockHiddenInput' ke 0
            currentStockHiddenInput.value = '0';
        }
        calculatePredictedStock(); // Hitung ulang stok prediksi
    });

    // --- Event Listener untuk Perubahan Jenis Transaksi dan Kuantitas ---
    transactionJenis.addEventListener('change', calculatePredictedStock);
    transactionKuantitas.addEventListener('input', calculatePredictedStock);

    // --- Fungsi untuk Menghitung Stok Prediksi ---
    function calculatePredictedStock() {
        const currentStockValue = parseInt(currentStockHiddenInput.value); // Ambil stok saat ini dari input hidden
        const quantity = parseInt(transactionKuantitas.value || '0'); // Ambil kuantitas, default 0 jika kosong/invalid
        const transactionType = transactionJenis.value; // Ambil jenis transaksi

        // Pastikan nilai valid dan barang sudah dipilih sebelum menghitung
        if (!isNaN(currentStockValue) && !isNaN(quantity) && transactionBarangId.value !== "") {
            let calculatedPredictedStock = currentStockValue;
            if (transactionType === 'masuk') {
                calculatedPredictedStock = currentStockValue + quantity;
            } else if (transactionType === 'keluar') {
                calculatedPredictedStock = currentStockValue - quantity;
            }
            predictedStock.value = calculatedPredictedStock; // Update input 'Stok Setelah Transaksi'
        } else {
            predictedStock.value = '0'; // Set ke 0 jika tidak bisa dihitung
        }

        // Tampilkan/sembunyikan field Pemasok berdasarkan jenis transaksi
        if (transactionType === 'masuk') {
            pemasokField.classList.remove('hidden');
            transactionPemasokId.setAttribute('required', 'required'); // Pemasok wajib untuk transaksi masuk
        } else {
            pemasokField.classList.add('hidden');
            transactionPemasokId.removeAttribute('required');
            transactionPemasokId.value = ''; // Kosongkan pilihan pemasok jika disembunyikan
        }
    }

    // --- Handler untuk Submit Form Transaksi (via AJAX) ---
    transactionForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Mencegah reload halaman

        // --- Validasi Client-Side Sederhana ---
        if (transactionBarangId.value === "") {
            alert("Harap pilih barang.");
            return;
        }
        if (transactionJenis.value === "") {
            alert("Harap pilih jenis transaksi.");
            return;
        }
        const quantity = parseInt(transactionKuantitas.value);
        if (isNaN(quantity) || quantity <= 0) {
            alert("Kuantitas harus angka positif.");
            return;
        }
        if (transactionJenis.value === 'keluar' && parseInt(predictedStock.value) < 0) {
            alert("Stok tidak cukup untuk transaksi keluar ini. Stok tersedia: " + currentStockHiddenInput.value); // Gunakan currentStockHiddenInput
            return;
        }
        if (transactionJenis.value === 'masuk' && pemasokField.classList.contains('hidden') === false && transactionPemasokId.value === "") {
            alert("Pemasok wajib diisi untuk transaksi masuk.");
            return;
        }

        // Kumpulkan data form
        const formData = new FormData(this);
        // Tambahkan nilai stok saat ini dan stok prediksi ke FormData untuk validasi backend atau logging
        formData.append('current_stock_at_transaction', currentStockHiddenInput.value); // Kirim stok saat ini dari input hidden
        formData.append('predicted_stock_after_transaction', predictedStock.value);

        // Kirim data menggunakan Fetch API
        fetch('../api/process_transaction.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                // Tangani error HTTP (misal: 404, 500)
                throw new Error('Respons jaringan tidak oke: ' + response.statusText);
            }
            return response.json(); // Parse respons JSON
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeTransactionModal(); // Tutup modal
                location.reload(); // Muat ulang halaman untuk menampilkan data terbaru
            } else {
                alert('Error: ' + data.message); // Tampilkan pesan error dari server
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses transaksi: ' + error.message);
        });
    });

    // --- Filtering Logic (Tidak banyak perubahan, hanya penyesuaian kecil) ---
    function applyFilters() {
        const rows = Array.from(transactionTableBody.getElementsByTagName('tr'));
        const selectedType = transactionTypeFilter.value.toLowerCase();
        const startDate = startDateFilter.value ? new Date(startDateFilter.value + 'T00:00:00') : null;
        // Perbaikan: endDateFilter harus mencakup seluruh hari.
        // Jika endDateFilter.value ada, kita buat objek Date sampai akhir hari (23:59:59).
        const endDate = endDateFilter.value ? new Date(endDateFilter.value + 'T23:59:59') : null;

        let anyDataRowVisible = false;
        let noDataRowCurrentlyVisible = false; // Flag untuk melacak status baris "Tidak ada data"

        rows.forEach(row => {
            const isNoDataRow = row.querySelector('td[colspan="9"]') !== null;

            if (isNoDataRow) {
                row.style.display = 'none'; // Sembunyikan sementara baris "tidak ada data"
                return;
            }

            const typeCell = row.children[3].textContent.trim().toLowerCase();
            // Parsing tanggal dari format 'DD-MM-YYYY HH:MM' ke Date object
            const dateParts = row.children[1].textContent.trim().split(' ')[0].split('-'); // Ambil 'DD-MM-YYYY'
            const transactionDate = new Date(`${dateParts[2]}-${dateParts[1]}-${dateParts[0]}T00:00:00`); // 'YYYY-MM-DD'

            const typeMatch = selectedType === '' || typeCell.includes(selectedType);
            const dateMatch = (!startDate || transactionDate >= startDate) && (!endDate || transactionDate <= endDate); // Perbaiki kondisi endDate

            if (typeMatch && dateMatch) {
                row.style.display = '';
                anyDataRowVisible = true;
            } else {
                row.style.display = 'none';
            }
        });

        const noDataRowElement = transactionTableBody.querySelector('td[colspan="9"]');
        if (noDataRowElement) {
            if (!anyDataRowVisible) {
                noDataRowElement.closest('tr').style.display = ''; // Tampilkan "tidak ada data" jika tidak ada baris lain
                noDataRowCurrentlyVisible = true;
            } else {
                noDataRowElement.closest('tr').style.display = 'none'; // Sembunyikan jika ada baris data yang terlihat
            }
        }

        renderPaginationButtons(); // Render ulang tombol pagination setelah filter
    }

    transactionTypeFilter.addEventListener('change', applyFilters);
    startDateFilter.addEventListener('change', applyFilters);
    endDateFilter.addEventListener('change', applyFilters);

    // --- Pagination Logic (Disesuaikan agar lebih 'nyambung' dengan filtering) ---
    const itemsPerPage = 10; // Jumlah item per halaman
    let currentPage = 1; // Halaman saat ini

    function renderPaginationButtons() {
        // Ambil semua baris data yang saat ini terlihat (sudah difilter)
        const allRows = Array.from(transactionTableBody.getElementsByTagName('tr'));
        const currentlyFilteredDataRows = allRows.filter(row =>
            row.style.display !== 'none' && row.querySelector('td[colspan="9"]') === null // Pastikan hanya baris data yang difilter dan terlihat
        );

        const totalPages = Math.ceil(currentlyFilteredDataRows.length / itemsPerPage);
        const paginationContainer = document.getElementById('paginationContainer');
        paginationContainer.innerHTML = ''; // Hapus tombol yang sudah ada

        // Jika hanya ada 1 halaman atau tidak ada data, sembunyikan pagination
        if (totalPages <= 1) { // Total pages <= 1 berarti tidak perlu pagination
             // Pastikan semua baris data yang difilter terlihat jika tidak ada pagination
            currentlyFilteredDataRows.forEach(row => row.style.display = '');
            return;
        }

        // Sesuaikan currentPage jika di luar batas setelah filtering
        if (currentPage > totalPages) {
            currentPage = totalPages;
        }
        if (currentPage < 1) {
            currentPage = 1;
        }

        // Tombol "Sebelumnya"
        if (currentPage > 1) {
            const prevButton = document.createElement('button');
            prevButton.textContent = 'Sebelumnya';
            prevButton.classList.add('px-3', 'py-1', 'border', 'rounded-md', 'hover:bg-blue-200', 'focus:outline-none', 'mr-2');
            prevButton.addEventListener('click', () => {
                currentPage--;
                displayPage(currentPage);
                renderPaginationButtons(); // Render ulang tombol untuk update status aktif
            });
            paginationContainer.appendChild(prevButton);
        }

        // Tombol angka halaman
        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.classList.add('px-3', 'py-1', 'border', 'rounded-md', 'hover:bg-blue-200', 'focus:outline-none', 'mx-0.5');
            if (i === currentPage) {
                button.classList.remove('bg-gray-200', 'text-gray-700');
                button.classList.add('bg-blue-500', 'text-white');
            } else {
                button.classList.remove('bg-blue-500', 'text-white');
                button.classList.add('bg-gray-200', 'text-gray-700');
            }
            button.addEventListener('click', () => {
                currentPage = i;
                displayPage(currentPage);
                renderPaginationButtons(); // Render ulang tombol untuk update status aktif
            });
            paginationContainer.appendChild(button);
        }

        // Tombol "Selanjutnya"
        if (currentPage < totalPages) {
            const nextButton = document.createElement('button');
            nextButton.textContent = 'Selanjutnya';
            nextButton.classList.add('px-3', 'py-1', 'border', 'rounded-md', 'hover:bg-blue-200', 'focus:outline-none', 'ml-2');
            nextButton.addEventListener('click', () => {
                currentPage++;
                displayPage(currentPage);
                renderPaginationButtons(); // Render ulang tombol untuk update status aktif
            });
            paginationContainer.appendChild(nextButton);
        }

        displayPage(currentPage); // Pastikan halaman yang benar ditampilkan setelah tombol dirender
    }

    function displayPage(page) {
        const allRows = Array.from(transactionTableBody.getElementsByTagName('tr'));
        const currentlyFilteredDataRows = allRows.filter(row =>
            row.style.display !== 'none' && row.querySelector('td[colspan="9"]') === null
        );

        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        currentlyFilteredDataRows.forEach((row, index) => { // Loop hanya pada baris data yang difilter
            if (index >= start && index < end) {
                row.style.display = ''; // Tampilkan untuk halaman saat ini
            } else {
                row.style.display = 'none'; // Sembunyikan karena pagination
            }
        });

        // Tangani baris "tidak ada data" secara terpisah setelah pagination
        const noDataRowElement = transactionTableBody.querySelector('td[colspan="9"]');
        if (noDataRowElement) {
             if (currentlyFilteredDataRows.length === 0) {
                 noDataRowElement.closest('tr').style.display = ''; // Tampilkan jika tidak ada data yang difilter sama sekali
             } else {
                 noDataRowElement.closest('tr').style.display = 'none'; // Sembunyikan jika ada data
             }
        }
    }

    // --- Panggilan Inisialisasi Awal ---
    // Panggil applyFilters() sekali saat halaman dimuat.
    // applyFilters() akan secara otomatis memanggil renderPaginationButtons() di dalamnya.
    applyFilters();
});