<?php
require_once __DIR__ . '/../../src/db.php'; // Sesuaikan path jika perlu

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

// Pastikan request method adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

// Ambil data JSON dari body request
$data = json_decode(file_get_contents('php://input'), true);

// Validasi data
$barang_id = filter_var($data['barang_id'] ?? '', FILTER_VALIDATE_INT);
$jenis = $data['jenis'] ?? '';
$kuantitas = filter_var($data['kuantitas'] ?? '', FILTER_VALIDATE_INT);
$pemasok_id = filter_var($data['pemasok_id'] ?? '', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE); // Boleh NULL
$catatan = $data['catatan'] ?? '';

// Basic validation
if (!$barang_id || !in_array($jenis, ['masuk', 'keluar']) || !$kuantitas || $kuantitas <= 0) {
    $response['message'] = 'Data transaksi tidak lengkap atau tidak valid.';
    echo json_encode($response);
    exit;
}

// Mulai transaksi database
$conn->begin_transaction();

try {
    // 1. Ambil stok barang saat ini
    $stmt_stok = $conn->prepare("SELECT stok FROM barang WHERE id = ? FOR UPDATE"); // FOR UPDATE untuk locking
    if (!$stmt_stok) {
        throw new Exception("Prepare failed (stok): " . $conn->error);
    }
    $stmt_stok->bind_param("i", $barang_id);
    $stmt_stok->execute();
    $result_stok = $stmt_stok->get_result();
    $barang_data = $result_stok->fetch_assoc();
    $stmt_stok->close();

    if (!$barang_data) {
        throw new Exception("Barang dengan ID " . $barang_id . " tidak ditemukan.");
    }

    $stok_saat_ini = $barang_data['stok'];
    $stok_sesudah = $stok_saat_ini;

    // 2. Hitung stok sesudah
    if ($jenis === 'masuk') {
        $stok_sesudah += $kuantitas;
    } elseif ($jenis === 'keluar') {
        $stok_sesudah -= $kuantitas;
        if ($stok_sesudah < 0) {
            throw new Exception("Stok tidak mencukupi untuk transaksi keluar ini. Stok saat ini: " . $stok_saat_ini);
        }
    }

    // 3. Update stok barang di tabel barang
    $stmt_update_stok = $conn->prepare("UPDATE barang SET stok = ? WHERE id = ?");
    if (!$stmt_update_stok) {
        throw new Exception("Prepare failed (update stok): " . $conn->error);
    }
    $stmt_update_stok->bind_param("ii", $stok_sesudah, $barang_id);
    if (!$stmt_update_stok->execute()) {
        throw new Exception("Gagal update stok barang: " . $stmt_update_stok->error);
    }
    $stmt_update_stok->close();

    // 4. Masukkan data transaksi ke tabel transaksi
    $stmt_insert_transaksi = $conn->prepare(
        "INSERT INTO transaksi (barang_id, jenis, kuantitas, stok_sesudah, pemasok_id, catatan) VALUES (?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt_insert_transaksi) {
        throw new Exception("Prepare failed (insert transaksi): " . $conn->error);
    }
    // Perhatikan tipe parameter: i (integer), s (string), i (integer), i (integer), i (integer/nullable), s (string)
    // Untuk pemasok_id yang bisa NULL, bisa tetap pakai 'i' jika PHP-nya mengkonversi NULL ke 0,
    // atau gunakan string 's' dan pastikan di DB bisa terima string NULL atau 0.
    // Pilihan terbaik adalah memastikan kolom pemasok_id di DB bisa NULL dan bind null dengan "i"
    // atau jika itu default 0, pastikan 0 adalah nilai yang tepat jika tidak ada pemasok.
    // Di sini saya pakai "i" dan FILTER_NULL_ON_FAILURE di atas untuk handle NULL.
    $stmt_insert_transaksi->bind_param("isiiss", $barang_id, $jenis, $kuantitas, $stok_sesudah, $pemasok_id, $catatan);
    if (!$stmt_insert_transaksi->execute()) {
        throw new Exception("Gagal menyimpan transaksi: " . $stmt_insert_transaksi->error);
    }
    $stmt_insert_transaksi->close();

    // Commit transaksi jika semua berhasil
    $conn->commit();
    $response['success'] = true;
    $response['message'] = 'Transaksi berhasil dicatat dan stok diperbarui!';

} catch (Exception $e) {
    // Rollback transaksi jika ada kesalahan
    $conn->rollback();
    $response['message'] = 'Gagal memproses transaksi: ' . $e->getMessage();
    // Log error di server
    // error_log('Transaction Error: ' . $e->getMessage());
} finally {
    $conn->close();
}

echo json_encode($response);
?>