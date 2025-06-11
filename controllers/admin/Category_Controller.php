<?php
// UAP_WEB/controllers/admin/Category_Controller.php

require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/functions.php';

class Category_Controller {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function index() {
        $categories = Category_Model::get_all_categories($this->conn);
        return [
            'view' => 'admin/category_list.php',
            'data' => [
                'page_title' => 'Manajemen Kategori Barang',
                'categories' => $categories,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function add() {
        $errors = [];
        $category_data = ['name' => '', 'description' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_data['name'] = sanitize_user_input($_POST['name']);
            $category_data['description'] = sanitize_user_input($_POST['description']);

            if (empty($category_data['name'])) {
                $errors[] = "Nama kategori wajib diisi.";
            }

            if (empty($errors)) {
                if (Category_Model::create_category($this->conn, $category_data)) {
                    set_flash_message("Kategori '{$category_data['name']}' berhasil ditambahkan.", "success");
                    redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/categories');
                } else {
                    $errors[] = "Error menambahkan kategori. Silakan coba lagi.";
                }
            }
        }

        return [
            'view' => 'admin/category_add.php',
            'data' => [
                'page_title' => 'Tambah Kategori Barang',
                'category' => $category_data,
                'errors' => $errors,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function edit($id) {
        $errors = [];
        $category_data = Category_Model::get_category_by_id($this->conn, $id);

        if (!$category_data) {
            set_flash_message("Kategori tidak ditemukan.", "error");
            redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/categories');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_data['name'] = sanitize_user_input($_POST['name']);
            $category_data['description'] = sanitize_user_input($_POST['description']);

            if (empty($category_data['name'])) {
                $errors[] = "Nama kategori wajib diisi.";
            }

            if (empty($errors)) {
                if (Category_Model::update_category($this->conn, $id, $category_data)) {
                    set_flash_message("Kategori '{$category_data['name']}' berhasil diperbarui.", "success");
                    redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/categories');
                } else {
                    $errors[] = "Error memperbarui kategori. Silakan coba lagi.";
                }
            }
        }

        return [
            'view' => 'admin/category_edit.php',
            'data' => [
                'page_title' => 'Edit Kategori Barang',
                'category_id' => $id,
                'category' => $category_data,
                'errors' => $errors,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function delete($id) {
        if (Category_Model::delete_category($this->conn, $id)) {
            set_flash_message("Kategori berhasil dihapus.", "success");
        } else {
            set_flash_message("Error menghapus kategori.", "error");
        }
        redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/categories');
    }
}
?>