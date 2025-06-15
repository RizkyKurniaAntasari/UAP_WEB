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
    harga_beli DECIMAL(10, 2) NOT NULL,
    harga_jual DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (id_pemasok) REFERENCES pemasok(id) ON DELETE RESTRICT ON UPDATE CASCADE
);