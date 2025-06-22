<?php
include_once __DIR__ . '/../../src/db.php';
include_once __DIR__ . '/../../src/functions.php';

// === Hapus User ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    $id = $_POST['hapus_id'];
    $query = "DELETE FROM users WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        die("Gagal menghapus pengguna: " . mysqli_error($conn));
    }
}
redirect_from_controllers('/users.php');