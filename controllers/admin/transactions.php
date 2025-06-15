<?php
// --- 1. Load koneksi database ---
require_once __DIR__ . '/../../src/db.php';

// --- 2. Fungsi ambil semua transaksi ---
function getAllTransactions(mysqli $conn): array {
    $sql = "
      SELECT
        t.id,
        t.tanggal,
        t.barang_id,
        t.jenis,
        t.kuantitas,
        t.stok_sebelum,
        t.stok_sesudah,
        t.pemasok_id,
        t.catatan
      FROM transaksi t
      ORDER BY t.tanggal DESC
    ";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// --- 3. Handle Create & Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = $_POST['transactionId']        ?? ''; // dari form modal
    $barang_id  = intval($_POST['transactionBarangId'] ?? 0);
    $jenis      = $_POST['transactionJenis']     ?? 'masuk';
    $kuantitas  = intval($_POST['transactionKuantitas'] ?? 0);
    $pemasok_id = $_POST['transactionPemasokId'] !== ''
                    ? intval($_POST['transactionPemasokId'])
                    : null;
    $catatan    = $_POST['transactionCatatan']   ?? '';
    $tanggal    = date('Y-m-d H:i:s');

    // --- Ambil stok sekarang ---
    $stok_awal = 0;
    $stok_result = $conn->query("SELECT stok FROM barang WHERE id = $barang_id");
    if ($stok_result && $stok_result->num_rows > 0) {
        $stok_data = $stok_result->fetch_assoc();
        $stok_awal = intval($stok_data['stok']);
    }

    // Hitung stok setelah transaksi
    $stok_akhir = $jenis === 'masuk'
        ? $stok_awal + $kuantitas
        : $stok_awal - $kuantitas;

    if ($id !== '') {
        // --- UPDATE ---
        $stmt = $conn->prepare("
            UPDATE transaksi
            SET tanggal=?, barang_id=?, jenis=?, kuantitas=?,
                stok_sebelum=?, stok_sesudah=?, pemasok_id=?, catatan=?
            WHERE id=?
        ");
        $stmt->bind_param(
            "sisiiissi",
            $tanggal,
            $barang_id,
            $jenis,
            $kuantitas,
            $stok_awal,
            $stok_akhir,
            $pemasok_id,
            $catatan,
            $id
        );
    } else {
        // --- INSERT ---
        $stmt = $conn->prepare("
            INSERT INTO transaksi
            (tanggal, barang_id, jenis, kuantitas,
             stok_sebelum, stok_sesudah, pemasok_id, catatan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "sisiiiss",
            $tanggal,
            $barang_id,
            $jenis,
            $kuantitas,
            $stok_awal,
            $stok_akhir,
            $pemasok_id,
            $catatan
        );
    }

    if ($stmt->execute()) {
        // Update stok barang di tabel barang
        $conn->query("UPDATE barang SET stok = $stok_akhir WHERE id = $barang_id");
    }

    $stmt->close();

    // Kembali ke halaman utama
    header("Location: ../../views/admin/transactions.php");
    exit;
}

// --- 4. Handle Delete ---
if (isset($_GET['hapus'])) {
    $hapusId = intval($_GET['hapus']);
    $stmt = $conn->prepare("DELETE FROM transaksi WHERE id=?");
    $stmt->bind_param("i", $hapusId);
    $stmt->execute();
    $stmt->close();

    header("Location: ../../views/admin/transactions.php");
    exit;
}

// --- 5. Ambil semua transaksi ---
$all_transactions = getAllTransactions($conn);
