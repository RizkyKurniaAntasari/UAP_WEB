CREATE DATABASE IF NOT EXISTS sintory_db;
USE sintory_db;

CREATE TABLE pemasok (
  id INT AUTO_INCREMENT PRIMARY KEY,
  perusahaan VARCHAR(255),
  kontak VARCHAR(255),
  email VARCHAR(255),
  telepon VARCHAR(20),
  alamat TEXT
);

CREATE TABLE kategori(
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(255),
  deskripsi VARCHAR(255)
);