-- Buat database jika belum ada dan gunakan
CREATE DATABASE IF NOT EXISTS sintory_db;
USE sintory_db;

-- 1. Tabel users (Tidak ada perubahan, dibuat paling awal karena direferensikan)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL, -- Menambahkan NOT NULL karena nama penting
    email VARCHAR(255) NOT NULL UNIQUE, -- Menambahkan NOT NULL dan UNIQUE untuk email
    password VARCHAR(255) NOT NULL, -- Menambahkan NOT NULL
    role VARCHAR(255) NOT NULL, -- Menambahkan NOT NULL (misal: 'admin', 'user', 'supplier')
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Tabel kategori (Dibuat sebelum 'barang' karena direferensikan)
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(255) NOT NULL UNIQUE,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Tabel pemasok (Dibuat setelah 'users' karena mereferensikan, dan sebelum 'barang')
-- Menggabungkan semua kolom relevan dari kedua definisi pemasok sebelumnya
CREATE TABLE IF NOT EXISTS pemasok (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE, -- Jika setiap pemasok hanya bisa diwakili oleh satu user (1-to-1)
                         -- Hapus UNIQUE jika satu user bisa menjadi perwakilan beberapa pemasok (1-to-many)
    perusahaan VARCHAR(255),
    kontak VARCHAR(255) NOT NULL, -- Kolom nama yang Anda gunakan di PHP
    email VARCHAR(255),
    telepon VARCHAR(20),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Kunci Asing yang Benar: user_id merujuk ke users.id
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
    -- ON DELETE SET NULL: Jika user dihapus, user_id di pemasok menjadi NULL.
    -- Anda bisa ganti dengan ON DELETE CASCADE jika ingin pemasok ikut terhapus.
    -- Atau ON DELETE RESTRICT jika tidak boleh menghapus user yang masih terhubung ke pemasok.
);

-- 4. Tabel barang (Dibuat paling akhir karena mereferensikan 'kategori' dan 'pemasok')
CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) NOT NULL,
    id_kategori INT NOT NULL,
    id_pemasok INT NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    satuan VARCHAR(50), -- Contoh: 'pcs', 'kg', 'liter' - penting untuk kuantitas
    harga_beli DECIMAL(10, 2) NOT NULL,
    harga_jual DECIMAL(10, 2) NOT NULL,
    deskripsi TEXT NULL, -- Tambahan: untuk detail barang lebih lanjut
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (id_pemasok) REFERENCES pemasok(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- 5. Tabel transaksi (Mencatat semua pergerakan stok)
CREATE TABLE IF NOT EXISTS transaksi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP, -- Menggunakan DATETIME untuk tanggal dan waktu lengkap
    barang_id INT NOT NULL,                     -- Kunci asing ke tabel `barang`
    jenis ENUM('masuk', 'keluar') NOT NULL,     -- 'masuk' untuk penambahan stok, 'keluar' untuk pengurangan stok
    kuantitas INT NOT NULL,                     -- Jumlah barang dalam transaksi ini
    stok_sesudah INT NOT NULL,                  -- Stok barang SETELAH transaksi ini dilakukan (ini akan sama dengan stok_akhir di aplikasi)
    pemasok_id INT,                             -- Kunci asing ke tabel `pemasok`.ID (opsional, untuk transaksi masuk)
                                                -- Nama pemasok akan di-join dari tabel `pemasok` saat menampilkan data.
    catatan TEXT,                               -- Catatan tambahan untuk transaksi
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Kapan record transaksi ini dibuat
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Kapan record transaksi ini terakhir diupdate

    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (pemasok_id) REFERENCES pemasok(id) ON DELETE SET NULL ON UPDATE CASCADE
    -- ON DELETE SET NULL: Jika pemasok dihapus, `pemasok_id` di transaksi akan menjadi NULL.
    -- Ini menjaga riwayat transaksi tetap ada meskipun tanpa informasi pemasok.
    -- Pertimbangkan ON DELETE RESTRICT jika Anda ingin mencegah penghapusan pemasok yang sudah terkait transaksi.
);
-- -- Gunakan database
-- USE sintory_db;

-- -- Data Dummy untuk Tabel users
-- INSERT INTO users (nama, email, password, role) VALUES
-- ('Admin Utama', 'admin@example.com', 'password123', 'admin'),
-- ('Pengguna Biasa', 'user@example.com', 'userpass', 'user'),
-- ('Suply Jaya', 'supplier1@example.com', 'supplierpass1', 'supplier'),
-- ('Supplier Sejahtera', 'supplier2@example.com', 'supplierpass2', 'supplier'),
-- ('Manajer Gudang', 'gudang@example.com', 'gudangpass', 'user');

-- -- Data Dummy untuk Tabel kategori
-- INSERT INTO kategori (nama_kategori, deskripsi) VALUES
-- ('Elektronik', 'Berbagai macam produk elektronik rumah tangga dan pribadi.'),
-- ('Pakaian', 'Berbagai jenis pakaian pria, wanita, dan anak-anak.'),
-- ('Makanan & Minuman', 'Produk makanan dan minuman kemasan serta segar.'),
-- ('Peralatan Rumah Tangga', 'Perkakas dan peralatan untuk keperluan rumah tangga.');

-- -- Data Dummy untuk Tabel pemasok
-- -- Menghubungkan pemasok dengan user_id yang memiliki role 'supplier'
-- INSERT INTO pemasok (user_id, perusahaan, kontak, email, telepon, alamat) VALUES
-- ((SELECT id FROM users WHERE email = 'supplier1@example.com'), 'PT Elektronik Maju', 'Budi Santoso', 'info@elektronikmaju.com', '081234567890', 'Jl. Merdeka No. 10, Jakarta'),
-- ((SELECT id FROM users WHERE email = 'supplier2@example.com'), 'UD Fashion Trendi', 'Dewi Anggraini', 'contact@fashiontrendi.co.id', '085678901234', 'Jl. Gaya No. 20, Bandung'),
-- (NULL, 'CV Pangan Sehat', 'Agus Salim', 'admin@pangansehat.net', '087812345678', 'Jl. Sehat Selalu No. 5, Surabaya');
-- -- Pemasok terakhir tidak terhubung ke user_id, user_id akan menjadi NULL sesuai FOREIGN KEY ON DELETE SET NULL

-- -- Data Dummy untuk Tabel barang
-- INSERT INTO barang (nama_barang, id_kategori, id_pemasok, stok, satuan, harga_beli, harga_jual, deskripsi) VALUES
-- ('Laptop Gaming ABC', (SELECT id FROM kategori WHERE nama_kategori = 'Elektronik'), (SELECT id FROM pemasok WHERE perusahaan = 'PT Elektronik Maju'), 15, 'unit', 10000000.00, 12500000.00, 'Laptop performa tinggi untuk gaming dan desain grafis.'),
-- ('Kemeja Pria Casual', (SELECT id FROM kategori WHERE nama_kategori = 'Pakaian'), (SELECT id FROM pemasok WHERE perusahaan = 'UD Fashion Trendi'), 50, 'pcs', 75000.00, 120000.00, 'Kemeja katun nyaman untuk kegiatan sehari-hari.'),
-- ('Beras Premium 5kg', (SELECT id FROM kategori WHERE nama_kategori = 'Makanan & Minuman'), (SELECT id FROM pemasok WHERE perusahaan = 'CV Pangan Sehat'), 100, 'karung', 50000.00, 65000.00, 'Beras kualitas premium, pulen dan wangi.'),
-- ('Blender Serbaguna', (SELECT id FROM kategori WHERE nama_kategori = 'Peralatan Rumah Tangga'), (SELECT id FROM pemasok WHERE perusahaan = 'PT Elektronik Maju'), 20, 'unit', 300000.00, 450000.00, 'Blender multifungsi untuk dapur modern.'),
-- ('T-Shirt Wanita', (SELECT id FROM kategori WHERE nama_kategori = 'Pakaian'), (SELECT id FROM pemasok WHERE perusahaan = 'UD Fashion Trendi'), 70, 'pcs', 40000.00, 75000.00, 'Kaos wanita dengan desain simple dan stylish.');

-- -- Data Dummy untuk Tabel transaksi
-- -- Logika untuk stok_sebelum dan stok_sesudah harus dihitung secara manual atau oleh aplikasi
-- -- Karena FOREIGN KEY tidak dapat memvalidasi nilai numerik pada kolom lain.

-- -- Transaksi Masuk: Laptop Gaming ABC
-- INSERT INTO transaksi (barang_id, jenis, kuantitas, stok_sesudah, pemasok_id, catatan) VALUES
-- ((SELECT id FROM barang WHERE nama_barang = 'Laptop Gaming ABC'), 'masuk', 10, 25, (SELECT id FROM pemasok WHERE perusahaan = 'PT Elektronik Maju'), 'Pembelian tambahan stok dari pemasok.');
-- -- Update stok di tabel barang untuk mencerminkan transaksi ini
-- UPDATE barang SET stok = 25 WHERE nama_barang = 'Laptop Gaming ABC';

-- -- Transaksi Keluar: Kemeja Pria Casual
-- INSERT INTO transaksi (barang_id, jenis, kuantitas, stok_sebelum, stok_sesudah, pemasok_id, catatan) VALUES
-- ((SELECT id FROM barang WHERE nama_barang = 'Kemeja Pria Casual'), 'keluar', 5, 45, NULL, 'Penjualan ke pelanggan ritel.');
-- -- Update stok di tabel barang untuk mencerminkan transaksi ini
-- UPDATE barang SET stok = 45 WHERE nama_barang = 'Kemeja Pria Casual';

-- -- Transaksi Masuk: Beras Premium 5kg
-- INSERT INTO transaksi (barang_id, jenis, kuantitas, stok_sebelum, stok_sesudah, pemasok_id, catatan) VALUES
-- ((SELECT id FROM barang WHERE nama_barang = 'Beras Premium 5kg'), 'masuk', 20, 120, (SELECT id FROM pemasok WHERE perusahaan = 'CV Pangan Sehat'), 'Restock bulanan.');
-- -- Update stok di tabel barang untuk mencerminkan transaksi ini
-- UPDATE barang SET stok = 120 WHERE nama_barang = 'Beras Premium 5kg';

-- -- Transaksi Keluar: Blender Serbaguna
-- INSERT INTO transaksi (barang_id, jenis, kuantitas, stok_sebelum, stok_sesudah, pemasok_id, catatan) VALUES
-- ((SELECT id FROM barang WHERE nama_barang = 'Blender Serbaguna'), 'keluar', 2, 20, 18, NULL, 'Pesanan online.');
-- -- Update stok di tabel barang untuk mencerminkan transaksi ini
-- UPDATE barang SET stok = 18 WHERE nama_barang = 'Blender Serbaguna';

-- -- Transaksi Keluar: T-Shirt Wanita
-- INSERT INTO transaksi (barang_id, jenis, kuantitas, stok_sebelum, stok_sesudah, pemasok_id, catatan) VALUES
-- ((SELECT id FROM barang WHERE nama_barang = 'T-Shirt Wanita'), 'keluar', 10, 70, 60, NULL, 'Penjualan di toko fisik.');
-- -- Update stok di tabel barang untuk mencerminkan transaksi ini
-- UPDATE barang SET stok = 60 WHERE nama_barang = 'T-Shirt Wanita';
