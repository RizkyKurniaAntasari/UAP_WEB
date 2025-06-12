<?php
include_once __DIR__ . '/../../src/db.php';

// --- Tambah / Edit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $perusahaan = $_POST['perusahaan'] ?? '';
    $kontak = $_POST['kontak'] ?? '';
    $email = $_POST['email'] ?? '';
    $telepon = $_POST['telepon'] ?? '';
    $alamat = $_POST['alamat'] ?? '';

    if ($id) {
        // Edit
        $stmt = $conn->prepare("UPDATE pemasok SET perusahaan=?, kontak=?, email=?, telepon=?, alamat=? WHERE id=?");
        $stmt->bind_param("sssssi", $perusahaan, $kontak, $email, $telepon, $alamat, $id);
        $stmt->execute();
    } else {
        // Tambah
        $stmt = $conn->prepare("INSERT INTO pemasok (perusahaan, kontak, email, telepon, alamat) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $perusahaan, $kontak, $email, $telepon, $alamat);
        $stmt->execute();
    }

    header("Location: ../../views/admin/suppliers.php");
    exit;
}

// --- Hapus ---
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM pemasok WHERE id=$id");
    header("Location: ../../views/admin/suppliers.php");
    exit;
}
?>
