<?php
// UAP_WEB/controllers/pemasok/Supplier_Controller.php

require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/functions.php';

class Supplier_Controller {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function index() {
        $suppliers = Supplier_Model::get_all_suppliers($this->conn);
        return [
            'view' => 'pemasok/supplier_list.php',
            'data' => [
                'page_title' => 'Manajemen Pemasok',
                'suppliers' => $suppliers,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function add() {
        $errors = [];
        $supplier_data = [
            'name' => '', 'contact_person' => '', 'phone' => '', 'email' => '', 'address' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $supplier_data['name'] = sanitize_user_input($_POST['name']);
            $supplier_data['contact_person'] = sanitize_user_input($_POST['contact_person']);
            $supplier_data['phone'] = sanitize_user_input($_POST['phone']);
            $supplier_data['email'] = sanitize_user_input($_POST['email']);
            $supplier_data['address'] = sanitize_user_input($_POST['address']);

            if (empty($supplier_data['name'])) {
                $errors[] = "Nama pemasok wajib diisi.";
            }
            if (!empty($supplier_data['email']) && !filter_var($supplier_data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format email tidak valid.";
            }

            if (empty($errors)) {
                if (Supplier_Model::create_supplier($this->conn, $supplier_data)) {
                    set_flash_message("Pemasok '{$supplier_data['name']}' berhasil ditambahkan.", "success");
                    redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/pemasok/suppliers');
                } else {
                    $errors[] = "Error menambahkan pemasok. Silakan coba lagi.";
                }
            }
        }

        return [
            'view' => 'pemasok/supplier_add.php',
            'data' => [
                'page_title' => 'Tambah Pemasok',
                'supplier' => $supplier_data,
                'errors' => $errors,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function edit($id) {
        $errors = [];
        $supplier_data = Supplier_Model::get_supplier_by_id($this->conn, $id);

        if (!$supplier_data) {
            set_flash_message("Pemasok tidak ditemukan.", "error");
            redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/pemasok/suppliers');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $supplier_data['name'] = sanitize_user_input($_POST['name']);
            $supplier_data['contact_person'] = sanitize_user_input($_POST['contact_person']);
            $supplier_data['phone'] = sanitize_user_input($_POST['phone']);
            $supplier_data['email'] = sanitize_user_input($_POST['email']);
            $supplier_data['address'] = sanitize_user_input($_POST['address']);

            if (empty($supplier_data['name'])) {
                $errors[] = "Nama pemasok wajib diisi.";
            }
            if (!empty($supplier_data['email']) && !filter_var($supplier_data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format email tidak valid.";
            }

            if (empty($errors)) {
                if (Supplier_Model::update_supplier($this->conn, $id, $supplier_data)) {
                    set_flash_message("Pemasok '{$supplier_data['name']}' berhasil diperbarui.", "success");
                    redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/pemasok/suppliers');
                } else {
                    $errors[] = "Error memperbarui pemasok. Silakan coba lagi.";
                }
            }
        }

        return [
            'view' => 'pemasok/supplier_edit.php',
            'data' => [
                'page_title' => 'Edit Pemasok',
                'supplier_id' => $id,
                'supplier' => $supplier_data,
                'errors' => $errors,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function delete($id) {
        if (Supplier_Model::delete_supplier($this->conn, $id)) {
            set_flash_message("Pemasok berhasil dihapus.", "success");
        } else {
            set_flash_message("Error menghapus pemasok.", "error");
        }
        redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/pemasok/suppliers');
    }
}
?>
