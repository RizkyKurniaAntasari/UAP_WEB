CREATE DATABASE IF NOT EXISTS sintory_db;
USE sintory_db;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(255),
  email VARCHAR(255),
  password VARCHAR(255),
  role VARCHAR(255)
);

CREATE TABLE pemasok (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  perusahaan VARCHAR(255),
  kontak VARCHAR(255),
  email VARCHAR(255),
  telepon VARCHAR(20),
  alamat TEXT,
  FOREIGN KEY (id) REFERENCES users(id)
);

CREATE TABLE kategori(
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(255),
  deskripsi VARCHAR(255)
);