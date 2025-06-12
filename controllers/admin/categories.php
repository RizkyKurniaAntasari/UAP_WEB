<?php
// Include the database connection file
include_once __DIR__ . '/../../src/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nama_kategori = $_POST['nama_kategori'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';

    // Basic validation (you might want to add more robust validation)
    if (empty($nama_kategori)) {
        header("Location: ../../views/admin/categories.php?error=nama_kategori_empty");
        exit;
    }

    if ($id) {
        // Edit existing category
        $stmt = $conn->prepare("UPDATE kategori SET nama_kategori=?, deskripsi=? WHERE id=?");
        if ($stmt === false) {
            error_log("Prepare failed: " . $conn->error);
            header("Location: ../../views/admin/categories.php?error=prepare_failed");
            exit;
        }
        $stmt->bind_param("ssi", $nama_kategori, $deskripsi, $id);

        if ($stmt->execute()) {
            header("Location: ../../views/admin/categories.php?status=edited");
        } else {
            error_log("Error updating category: " . $stmt->error);
            header("Location: ../../views/admin/categories.php?error=update_failed");
        }
        $stmt->close();
    } else {
        // Add new category
        $stmt = $conn->prepare("INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)");
        if ($stmt === false) {
            error_log("Prepare failed: " . $conn->error);
            header("Location: ../../views/admin/categories.php?error=prepare_failed");
            exit;
        }
        $stmt->bind_param("ss", $nama_kategori, $deskripsi);

        if ($stmt->execute()) {
            header("Location: ../../views/admin/categories.php?status=added");
        } else {
            error_log("Error adding category: " . $stmt->error);
            header("Location: ../../views/admin/categories.php?error=add_failed");
        }
        $stmt->close();
    }
    exit; // Important to exit after redirect
}

// --- Hapus ---
if (isset($_GET['delete'])) { // Changed 'hapus' to 'delete' for consistency with view
    $id = intval($_GET['delete']); // Ensure ID is an integer for security

    $stmt = $conn->prepare("DELETE FROM kategori WHERE id=?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        header("Location: ../../views/admin/categories.php?error=prepare_failed_delete");
        exit;
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../../views/admin/categories.php?status=deleted");
    } else {
        error_log("Error deleting category: " . $stmt->error);
        header("Location: ../../views/admin/categories.php?error=delete_failed");
    }
    $stmt->close();
    exit; // Important to exit after redirect
}

// --- Data Fetching for Display ---
// This part fetches data from the database to populate the table.
try {
    $stmt = $conn->prepare("SELECT id, nama_kategori, deskripsi FROM kategori ORDER BY id DESC");
    $stmt->execute();
    $categories = $stmt->get_result();
} catch (mysqli_sql_exception $e) {
    print_r("Database error fetching categories: " . $e->getMessage());
    $categories = false;
    $error_message = "Terjadi kesalahan saat memuat kategori. Silakan coba lagi nanti.";
}