<?php
// UAP_WEB/controllers/admin/Transaction_Controller.php

require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/functions.php';

class Transaction_Controller {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function index() {
        $transactions = Transaction_Model::get_all_transactions($this->conn);
        return [
            'view' => 'admin/transaction_list.php',
            'data' => [
                'page_title' => 'Manajemen Transaksi',
                'transactions' => $transactions,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function add() {
        $errors = [];
        $transaction_data = [
            'product_id' => '', 'type' => '', 'quantity' => 0, 'notes' => ''
        ];
        $products = Product_Model::get_all_products($this->conn);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $transaction_data['product_id'] = (int)sanitize_user_input($_POST['product_id']);
            $transaction_data['type'] = sanitize_user_input($_POST['type']);
            $transaction_data['quantity'] = (int)$_POST['quantity'];
            $transaction_data['notes'] = sanitize_user_input($_POST['notes']);
            $transaction_data['user_id'] = $_SESSION['user_id'];

            if (empty($transaction_data['product_id'])) {
                $errors[] = "Produk wajib dipilih.";
            }
            if (empty($transaction_data['type']) || !in_array($transaction_data['type'], ['in', 'out'])) {
                $errors[] = "Tipe transaksi tidak valid.";
            }
            if ($transaction_data['quantity'] <= 0) {
                $errors[] = "Kuantitas harus lebih dari 0.";
            }

            if (empty($errors)) {
                if (Transaction_Model::create_transaction($this->conn, $transaction_data)) {
                    set_flash_message("Transaksi berhasil dicatat dan stok diperbarui.", "success");
                    redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/transactions');
                } else {
                    // Error message set within Transaction_Model::create_transaction on rollback
                }
            }
        }

        return [
            'view' => 'admin/transaction_add.php',
            'data' => [
                'page_title' => 'Tambah Transaksi',
                'transaction' => $transaction_data,
                'products' => $products,
                'errors' => $errors,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function report() {
        $report_data = [];
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $selected_product_id = '';
        $selected_type = '';

        $products = Product_Model::get_all_products($this->conn);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $start_date = sanitize_user_input($_POST['start_date']);
            $end_date = sanitize_user_input($_POST['end_date']);
            $selected_product_id = sanitize_user_input($_POST['product_id']);
            $selected_type = sanitize_user_input($_POST['type']);

            if (empty($start_date) || empty($end_date)) {
                set_flash_message("Tanggal mulai dan tanggal akhir wajib diisi.", "error");
            } else if (strtotime($start_date) > strtotime($end_date)) {
                set_flash_message("Tanggal mulai tidak boleh lebih dari tanggal akhir.", "error");
            } else {
                $report_data = Transaction_Model::get_transactions_report(
                    $this->conn,
                    $start_date,
                    $end_date,
                    empty($selected_product_id) ? null : (int)$selected_product_id,
                    empty($selected_type) ? null : $selected_type
                );
            }
        }

        return [
            'view' => 'admin/transaction_report.php',
            'data' => [
                'page_title' => 'Laporan Transaksi Barang',
                'report_data' => $report_data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'products' => $products,
                'selected_product_id' => $selected_product_id,
                'selected_type' => $selected_type,
                'messages' => get_flash_messages()
            ]
        ];
    }
}
?>