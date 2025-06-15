<?php
// controllers/admin/products.php

// Include the database connection file
include_once __DIR__ . '/../../src/db.php';

// Function to set a flash message
function setFlashMessage($message, $type = 'success')
{
    setcookie('flash_message', $message, time() + 3600, '/'); // Expires in 1 hour
    setcookie('flash_type', $type, time() + 3600, '/'); // Expires in 1 hour
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_barang' || $action === 'edit_barang') {
        $id = $_POST['id'] ?? null; // Null for add, value for edit
        $nama_barang = $_POST['nama_barang'] ?? '';
        $id_kategori = $_POST['id_kategori'] ?? '';
        $id_pemasok = $_POST['id_pemasok'] ?? '';
        $stok = $_POST['stok'] ?? 0;
        $harga_beli = $_POST['harga_beli'] ?? 0.0;
        $harga_jual = $_POST['harga_jual'] ?? 0.0;

        // Basic validation
        if (empty($nama_barang) || empty($id_kategori) || empty($id_pemasok) || !is_numeric($stok) || !is_numeric($harga_beli) || !is_numeric($harga_jual)) {
            setFlashMessage('Semua bidang harus diisi dengan benar.', 'error');
            header("Location: ../../views/admin/products.php");
            exit;
        }

        if ($action === 'edit_barang' && $id) {
            // Edit existing product
            $stmt = $conn->prepare("UPDATE barang SET nama_barang=?, id_kategori=?, id_pemasok=?, stok=?, harga_beli=?, harga_jual=? WHERE id=?");
            if ($stmt === false) {
                error_log("Prepare failed: " . $conn->error);
                setFlashMessage('Gagal menyiapkan statement untuk update barang.', 'error');
                header("Location: ../../views/admin/products.php");
                exit;
            }
            $stmt->bind_param("siiidds", $nama_barang, $id_kategori, $id_pemasok, $stok, $harga_beli, $harga_jual, $id);

            if ($stmt->execute()) {
                setFlashMessage('Data barang berhasil diperbarui.');
                header("Location: ../../views/admin/products.php");
            } else {
                error_log("Error updating product: " . $stmt->error);
                setFlashMessage('Gagal memperbarui data barang.', 'error');
                header("Location: ../../views/admin/products.php");
            }
            $stmt->close();
        } elseif ($action === 'add_barang') {
            // Add new product
            $stmt = $conn->prepare("INSERT INTO barang (nama_barang, id_kategori, id_pemasok, stok, harga_beli, harga_jual) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                error_log("Prepare failed: " . $conn->error);
                setFlashMessage('Gagal menyiapkan statement untuk menambah barang.', 'error');
                header("Location: ../../views/admin/products.php");
                exit;
            }
            $stmt->bind_param("siiidd", $nama_barang, $id_kategori, $id_pemasok, $stok, $harga_beli, $harga_jual);

            if ($stmt->execute()) {
                setFlashMessage('Barang baru berhasil ditambahkan.');
                header("Location: ../../views/admin/products.php");
            } else {
                error_log("Error adding product: " . $stmt->error);
                setFlashMessage('Gagal menambahkan barang baru.', 'error');
                header("Location: ../../views/admin/products.php");
            }
            $stmt->close();
        }
    } elseif ($action === 'delete_barang') {
        $id = $_POST['id'] ?? null;

        if ($id) {
            $stmt = $conn->prepare("DELETE FROM barang WHERE id=?");
            if ($stmt === false) {
                error_log("Prepare failed: " . $conn->error);
                setFlashMessage('Gagal menyiapkan statement untuk menghapus barang.', 'error');
                header("Location: ../../views/admin/products.php");
                exit;
            }
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                setFlashMessage('Barang berhasil dihapus.');
                header("Location: ../../views/admin/products.php");
            } else {
                error_log("Error deleting product: " . $stmt->error);
                setFlashMessage('Gagal menghapus barang.', 'error');
                header("Location: ../../views/admin/products.php");
            }
            $stmt->close();
        } else {
            setFlashMessage('ID barang tidak ditemukan untuk dihapus.', 'error');
            header("Location: ../../views/admin/products.php");
        }
    }
    exit; // Important to exit after redirect
}

// --- Data Fetching for Display (for the products view itself) ---
// This block would typically be at the top of your products.php view file.
// It fetches product data along with category and supplier names using JOINs.
try {
    $query = "
        SELECT
            b.id,
            b.nama_barang,
            b.stok,
            b.harga_beli,
            b.harga_jual,
            b.id_kategori,
            b.id_pemasok,
            k.nama_kategori,
            p.kontak AS nama_pemasok
        FROM
            barang b
        LEFT JOIN
            kategori k ON b.id_kategori = k.id -- Corrected join condition
        LEFT JOIN
            pemasok p ON b.id_pemasok = p.id
        ORDER BY
            b.id DESC;
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $barang_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch options for categories and suppliers as well, as they are needed in the view's forms
    $kategori_options = $conn->query("SELECT id, nama_kategori FROM kategori")->fetch_all(MYSQLI_ASSOC);
    $pemasok_options = $conn->query("SELECT id, kontak FROM pemasok")->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    echo ("Database error fetching product data: " . $e->getMessage());
    $barang_list = [];
    $kategori_options = [];
    $pemasok_options = [];
    setFlashMessage("Terjadi kesalahan saat memuat data barang. Silakan coba lagi nanti.", 'error');
}
