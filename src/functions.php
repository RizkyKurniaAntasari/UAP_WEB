<?php
define('BASE_URL', '/' . basename(dirname(__DIR__))); // misal: '/inventaris'
password_hash("admin123", PASSWORD_DEFAULT);

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function redirect_views_admin($url) {
    header("Location: /views/admin" . $url);
    exit;
}

function redirect_views_pemasok($url) {
    header("Location: /views/pemasok" . $url);
    exit;
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function getKategori($id_kategori, $conn) {
    $sql = "SELECT nama_kategori FROM kategori WHERE id = '$id_kategori'";
    $result = mysqli_query($conn, $sql);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $nama_kategori = $row['nama_kategori'];
        return $nama_kategori;
    } else {
        return "Kategori tidak ditemukan.";
    }
}

function getNamaBarang($idBarang, $conn) {
    $sql = "SELECT nama_barang FROM barang WHERE id = '$idBarang'";
    $result = mysqli_query($conn, $sql);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $namaBarang = $row['nama_barang'];
        return $namaBarang;
    } else {
        return "Barang tidak ditemukan.";
    }
}

function getHargaBarang($idBarang, $jenis, $conn) {
    $sql = "";
    if ($jenis == "masuk") {
        $sql = "SELECT harga_beli FROM barang WHERE id = '$idBarang'";
        $result = mysqli_query($conn, $sql);
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $hargaBarang = $row['harga_beli'];
            return $hargaBarang;
        } else {
            return "Barang tidak ditemukan.";
        }
    } else if ($jenis == "keluar") {
        $sql = "SELECT harga_jual FROM barang WHERE id = '$idBarang'";
        $result = mysqli_query($conn, $sql);
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $hargaBarang = $row['harga_jual'];
            return $hargaBarang;
        } else {
            return "Barang tidak ditemukan.";
        }
    } else {
        return "Jenis tidak ditemukan.";
    }
    
}

function getIdKategori($kategori, $conn) {
    $sql = "SELECT id FROM kategori WHERE nama_kategori = '$kategori'";
    $result = mysqli_query($conn, $sql);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $id_kategori = $row['id'];
        return $id_kategori;
    } else {
        return 0;
    }
}

function getIdBarang($namaBarang, $conn) {
    $sql = "SELECT id FROM barang WHERE nama_barang LIKE '%{$namaBarang}%'";
    $idBarang = mysqli_query($conn, $sql);
    return $idBarang;
}

function formatRupiah(int $angka): string {
    return 'Rp ' . number_format($angka, 2, ',', '.');
}
?>