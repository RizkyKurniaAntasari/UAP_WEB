<?php
require_once __DIR__ . '/../../src/db.php'; // Sesuaikan path ke file koneksi database Anda

header('Content-Type: application/json'); // Memberi tahu klien bahwa respons adalah JSON

$response = ['success' => false, 'message' => 'Terjadi kesalahan tidak dikenal.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ambil dan sanitasi data dari request
    $barang_id = filter_input(INPUT_POST, 'transactionBarangId', FILTER_VALIDATE_INT);
    $jenis = filter_input(INPUT_POST, 'transactionJenis', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $kuantitas = filter_input(INPUT_POST, 'transactionKuantitas', FILTER_VALIDATE_INT);
    $pemasok_id = filter_input(INPUT_POST, 'transactionPemasokId', FILTER_VALIDATE_INT);
    $catatan = filter_input(INPUT_POST, 'transactionCatatan', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Ambil stok yang dikirim dari frontend (untuk logging/cross-check, bukan sumber kebenaran)
    // Sumber kebenaran stok harus selalu dari database
    $current_stock_at_transaction = filter_input(INPUT_POST, 'current_stock_at_transaction', FILTER_VALIDATE_INT);
    $predicted_stock_after_transaction = filter_input(INPUT_POST, 'predicted_stock_after_transaction', FILTER_VALIDATE_INT);


    // 2. Validasi Input
    if (!$conn) {
        $response['message'] = 'Koneksi database gagal.';
        echo json_encode($response);
        exit;
    }

    if ($barang_id === false || $barang_id === null || $kuantitas === false || $kuantitas === null || $kuantitas <= 0 || !in_array($jenis, ['masuk', 'keluar'])) {
        $response['message'] = 'Input tidak valid. Pastikan semua field wajib terisi dan kuantitas positif.';
        echo json_encode($response);
        exit;
    }

    if ($jenis === 'masuk' && ($pemasok_id === false || $pemasok_id === null)) {
        // Jika jenisnya 'masuk' dan pemasok tidak dipilih, ini adalah error.
        // Asumsi: untuk transaksi masuk, pemasok wajib diisi.
        // Jika tidak wajib, hapus validasi ini.
        $response['message'] = 'Pemasok wajib diisi untuk transaksi masuk.';
        echo json_encode($response);
        exit;
    }

    try {
        // Mulai transaksi database untuk memastikan integritas data
        $conn->begin_transaction();

        // 3. Ambil stok barang saat ini dari database
        $stmt_get_stock = $conn->prepare("SELECT stok FROM barang WHERE id = ? FOR UPDATE"); // Gunakan FOR UPDATE untuk locking
        $stmt_get_stock->bind_param("i", $barang_id);
        $stmt_get_stock->execute();
        $res_stock = $stmt_get_stock->get_result();

        if ($res_stock->num_rows === 0) {
            $response['message'] = 'Barang tidak ditemukan.';
            $conn->rollback();
            echo json_encode($response);
            exit;
        }

        $barang_data = $res_stock->fetch_assoc();
        $current_stock_db = $barang_data['stok'];

        $new_stock = $current_stock_db;

        // 4. Hitung stok baru dan lakukan validasi khusus untuk "keluar"
        if ($jenis === 'masuk') {
            $new_stock += $kuantitas;
        } elseif ($jenis === 'keluar') {
            if ($current_stock_db < $kuantitas) {
                $response['message'] = 'Stok tidak cukup untuk transaksi keluar ini. Stok tersedia: ' . $current_stock_db;
                $conn->rollback(); // Batalkan transaksi
                echo json_encode($response);
                exit;
            }
            $new_stock -= $kuantitas;
        }

        // 5. Perbarui stok di tabel barang
        $stmt_update_stock = $conn->prepare("UPDATE barang SET stok = ? WHERE id = ?");
        $stmt_update_stock->bind_param("ii", $new_stock, $barang_id);

        if (!$stmt_update_stock->execute()) {
            throw new Exception("Gagal memperbarui stok barang: " . $stmt_update_stock->error);
        }

        // 6. Masukkan transaksi baru ke tabel transaksi
        $stmt_insert_transaction = $conn->prepare(
            "INSERT INTO transaksi (tanggal, barang_id, jenis, kuantitas, stok_sesudah, pemasok_id, catatan) VALUES (NOW(), ?, ?, ?, ?, ?, ?)"
        );

        // Jika jenisnya 'keluar', pemasok_id bisa NULL. Jika 'masuk', pemasok_id akan memiliki nilai.
        // Jika pemasok_id tidak dipilih, set menjadi NULL di database
        $pemasok_id_for_db = ($jenis === 'masuk' && $pemasok_id !== null) ? $pemasok_id : null;
        $stmt_insert_transaction->bind_param(
            "isiiis",
            $barang_id,
            $jenis,
            $kuantitas,
            $new_stock,
            $pemasok_id_for_db, // Ini akan menjadi NULL jika $pemasok_id_for_db adalah NULL
            $catatan
        );

        if (!$stmt_insert_transaction->execute()) {
            throw new Exception("Gagal menyimpan transaksi: " . $stmt_insert_transaction->error);
        }

        // Jika semuanya berhasil, commit transaksi
        $conn->commit();
        $response['success'] = true;
        $response['message'] = 'Transaksi berhasil ditambahkan!';
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        $conn->rollback();
        $response['message'] = 'Error: ' . $e->getMessage();
        error_log("Transaction error: " . $e->getMessage());
    } finally {
        // Pastikan statement ditutup
        if (isset($stmt_get_stock)) {
            $stmt_get_stock->close();
        }
        if (isset($stmt_update_stock)) {
            $stmt_update_stock->close();
        }
        if (isset($stmt_insert_transaction)) {
            $stmt_insert_transaction->close();
        }
        $conn->close(); // Tutup koneksi database
    }
} else {
    $response['message'] = 'Metode request tidak diizinkan.';
}

echo json_encode($response);
