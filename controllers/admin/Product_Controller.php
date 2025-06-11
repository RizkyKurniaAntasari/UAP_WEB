<?php
// UAP_WEB/controllers/admin/Product_Controller.php

require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/functions.php';

class Product_Controller {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function index() {
        $products = Product_Model::get_all_products($this->conn);
        return [
            'view' => 'admin/product_list.php',
            'data' => [
                'page_title' => 'Manajemen Barang',
                'products' => $products,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function add() {
        $errors = [];
        $product_data = [
            'name' => '', 'category_id' => '', 'supplier_id' => '',
            'stock' => 0, 'price' => 0.00, 'description' => ''
        ];

        $categories = Category_Model::get_all_categories($this->conn);
        $suppliers = Supplier_Model::get_all_suppliers($this->conn);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_data['name'] = sanitize_user_input($_POST['name']);
            $product_data['category_id'] = sanitize_user_input($_POST['category_id']);
            $product_data['supplier_id'] = sanitize_user_input($_POST['supplier_id']);
            $product_data['stock'] = (int)$_POST['stock'];
            $product_data['price'] = (float)$_POST['price'];
            $product_data['description'] = sanitize_user_input($_POST['description']);

            if (empty($product_data['name'])) {
                $errors[] = "Nama barang wajib diisi.";
            }
            if ($product_data['stock'] < 0) {
                $errors[] = "Stok tidak boleh negatif.";
            }
            if ($product_data['price'] < 0) {
                $errors[] = "Harga tidak boleh negatif.";
            }

            if (empty($errors)) {
                if (Product_Model::create_product($this->conn, $product_data)) {
                    set_flash_message("Barang '{$product_data['name']}' berhasil ditambahkan.", "success");
                    redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/products');
                } else {
                    $errors[] = "Error menambahkan barang. Silakan coba lagi.";
                }
            }
        }

        return [
            'view' => 'admin/product_add.php',
            'data' => [
                'page_title' => 'Tambah Barang',
                'product' => $product_data,
                'categories' => $categories,
                'suppliers' => $suppliers,
                'errors' => $errors,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function edit($id) {
        $errors = [];
        $product_data = Product_Model::get_product_by_id($this->conn, $id);

        if (!$product_data) {
            set_flash_message("Barang tidak ditemukan.", "error");
            redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/products');
        }

        $categories = Category_Model::get_all_categories($this->conn);
        $suppliers = Supplier_Model::get_all_suppliers($this->conn);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_data['name'] = sanitize_user_input($_POST['name']);
            $product_data['category_id'] = sanitize_user_input($_POST['category_id']);
            $product_data['supplier_id'] = sanitize_user_input($_POST['supplier_id']);
            $product_data['stock'] = (int)$_POST['stock'];
            $product_data['price'] = (float)$_POST['price'];
            $product_data['description'] = sanitize_user_input($_POST['description']);

            if (empty($product_data['name'])) {
                $errors[] = "Nama barang wajib diisi.";
            }
            if ($product_data['stock'] < 0) {
                $errors[] = "Stok tidak boleh negatif.";
            }
            if ($product_data['price'] < 0) {
                $errors[] = "Harga tidak boleh negatif.";
            }

            if (empty($errors)) {
                if (Product_Model::update_product($this->conn, $id, $product_data)) {
                    set_flash_message("Barang '{$product_data['name']}' berhasil diperbarui.", "success");
                    redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/products');
                } else {
                    $errors[] = "Error memperbarui barang. Silakan coba lagi.";
                }
            }
        }

        return [
            'view' => 'admin/product_edit.php',
            'data' => [
                'page_title' => 'Edit Barang',
                'product_id' => $id,
                'product' => $product_data,
                'categories' => $categories,
                'suppliers' => $suppliers,
                'errors' => $errors,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function delete($id) {
        if (Product_Model::delete_product($this->conn, $id)) {
            set_flash_message("Barang berhasil dihapus.", "success");
        } else {
            set_flash_message("Error menghapus barang.", "error");
        }
        redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/products');
    }
}
?>