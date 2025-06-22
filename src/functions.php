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

function redirect_from_controllers($url){
    header("Location: ../../views/admin" . $url);
}

function keluar_bang(){
    header("Location: ../../logout.php");
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

function formatRupiah(int $angka): string {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>