<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/functions.php';
if (!$conn) {
    die("Koneksi database gagal.");
}

if (isset($_GET['id'])) {
    $id_transaksi = intval($_GET['id']);

    // Ambil detail transaksi
    $sql = "SELECT barang_id, kuantitas, jenis FROM transaksi WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_transaksi);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaksi = $result->fetch_assoc();
    $stmt->close();

    if ($transaksi) {
        $barang_id = $transaksi['barang_id'];
        $kuantitas = $transaksi['kuantitas'];
        $jenis = $transaksi['jenis'];

        // Hitung stok yang harus dikembalikan
        $rollback_stok = ($jenis === 'masuk') ? -$kuantitas : $kuantitas;

        // Update stok barang
        $update_sql = "UPDATE barang SET stok = stok + ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $rollback_stok, $barang_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Hapus transaksi
        $delete_sql = "DELETE FROM transaksi WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id_transaksi);
        if ($delete_stmt->execute()) {
            $delete_stmt->close();
            redirect_from_controllers('/transactions.php');
            exit;
        } else {
            error_log("Gagal hapus transaksi: " . $delete_stmt->error);
            $delete_stmt->close();
            redirect_from_controllers('/transactions.php');
            exit;
        }
    } else {
        redirect_from_controllers('/transactions.php');
        exit;
    }
} else {
    redirect_from_controllers('/transactions.php');
    exit;
}
