<?php
require_once __DIR__ . '/../../src/db.php'; // Sesuaikan path jika perlu

header('Content-Type: application/json'); // Penting untuk memberitahu browser bahwa ini JSON

$response = [
    'success' => false,
    'message' => '',
    'barang' => [],
    'pemasok' => []
];

try {
    // Ambil daftar barang
    $stmt_barang = $conn->prepare("SELECT id, nama_barang, stok FROM barang ORDER BY nama_barang ASC");
    if (!$stmt_barang) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt_barang->execute();
    $result_barang = $stmt_barang->get_result();
    while ($row = $result_barang->fetch_assoc()) {
        $response['barang'][] = $row;
    }
    $stmt_barang->close();

    // Ambil daftar pemasok
    // Perhatikan: di sini saya asumsikan tabel pemasok punya kolom 'nama_pemasok' atau sejenisnya
    // Jika 'kontak' adalah yang ingin ditampilkan, gunakan itu.
    $stmt_pemasok = $conn->prepare("SELECT id, kontak AS nama_pemasok FROM pemasok ORDER BY nama_pemasok ASC");
    if (!$stmt_pemasok) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt_pemasok->execute();
    $result_pemasok = $stmt_pemasok->get_result();
    while ($row = $result_pemasok->fetch_assoc()) {
        $response['pemasok'][] = $row;
    }
    $stmt_pemasok->close();

    $response['success'] = true;
    $response['message'] = 'Data berhasil diambil.';

} catch (Exception $e) {
    $response['message'] = 'Gagal mengambil data: ' . $e->getMessage();
    // Log error di server jika ini adalah lingkungan produksi
    // error_log('API Error: ' . $e->getMessage());
} finally {
    $conn->close();
}

echo json_encode($response);
?>