<?php
// UAP_WEB/controllers/admin/User_Controller.php

require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/functions.php';

class User_Controller {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function index() {
        $users = User_Model::get_all_users($this->conn);
        return [
            'view' => 'admin/user_list.php',
            'data' => [
                'page_title' => 'Manajemen Pengguna',
                'users' => $users,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function add() {
        $errors = [];
        $user_data = ['username' => '', 'password' => '', 'confirm_password' => '', 'role' => 'user'];
        $roles = ['admin', 'user'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_data['username'] = sanitize_user_input($_POST['username']);
            $user_data['password'] = $_POST['password'];
            $user_data['confirm_password'] = $_POST['confirm_password'];
            $user_data['role'] = sanitize_user_input($_POST['role']);

            if (empty($user_data['username']) || empty($user_data['password']) || empty($user_data['confirm_password']) || empty($user_data['role'])) {
                $errors[] = "Semua field wajib diisi.";
            } elseif ($user_data['password'] !== $user_data['confirm_password']) {
                $errors[] = "Password tidak cocok.";
            } elseif (strlen($user_data['password']) < 6) {
                $errors[] = "Password minimal 6 karakter.";
            } elseif (!in_array($user_data['role'], $roles)) {
                $errors[] = "Role pengguna tidak valid.";
            } else {
                $stmt_check = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
                $stmt_check->bind_param("s", $user_data['username']);
                $stmt_check->execute();
                $stmt_check->store_result();
                if ($stmt_check->num_rows > 0) {
                    $errors[] = "Username sudah ada. Pilih username lain.";
                }
                $stmt_check->close();
            }

            if (empty($errors)) {
                if (User_Model::create_user($this->conn, $user_data)) {
                    set_flash_message("Pengguna '{$user_data['username']}' berhasil ditambahkan.", "success");
                    redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/users');
                } else {
                    $errors[] = "Error menambahkan pengguna. Silakan coba lagi.";
                }
            }
        }

        return [
            'view' => 'admin/user_add.php',
            'data' => [
                'page_title' => 'Tambah Pengguna',
                'user' => $user_data,
                'roles' => $roles,
                'errors' => $errors,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function edit($id) {
        $errors = [];
        $user_data = User_Model::get_user_by_id($this->conn, $id);
        $roles = ['admin', 'user'];

        if (!$user_data) {
            set_flash_message("Pengguna tidak ditemukan.", "error");
            redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/users');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_data['username'] = sanitize_user_input($_POST['username']);
            $user_data['role'] = sanitize_user_input($_POST['role']);
            $new_password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if (empty($user_data['username']) || empty($user_data['role'])) {
                $errors[] = "Username dan Role wajib diisi.";
            } elseif (!in_array($user_data['role'], $roles)) {
                $errors[] = "Role pengguna tidak valid.";
            } else {
                if (!empty($new_password)) {
                    if ($new_password !== $confirm_password) {
                        $errors[] = "Password baru tidak cocok.";
                    } elseif (strlen($new_password) < 6) {
                        $errors[] = "Password baru minimal 6 karakter.";
                    }
                    $user_data['password'] = $new_password;
                } else {
                    $user_data['password'] = null;
                }

                $stmt_check = $this->conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                $stmt_check->bind_param("si", $user_data['username'], $id);
                $stmt_check->execute();
                $stmt_check->store_result();
                if ($stmt_check->num_rows > 0) {
                    $errors[] = "Username sudah digunakan oleh pengguna lain.";
                }
                $stmt_check->close();
            }

            if (empty($errors)) {
                if (User_Model::update_user($this->conn, $id, $user_data)) {
                    set_flash_message("Pengguna '{$user_data['username']}' berhasil diperbarui.", "success");
                    redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/users');
                } else {
                    $errors[] = "Error memperbarui pengguna. Silakan coba lagi.";
                }
            }
        }

        return [
            'view' => 'admin/user_edit.php',
            'data' => [
                'page_title' => 'Edit Pengguna',
                'user_id' => $id,
                'user' => $user_data,
                'roles' => $roles,
                'errors' => $errors,
                'messages' => get_flash_messages()
            ]
        ];
    }

    public function delete($id) {
        if ($_SESSION['user_id'] == $id) {
            set_flash_message("Tidak bisa menghapus akun Anda sendiri.", "error");
            redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/users');
            return;
        }

        if (User_Model::delete_user($this->conn, $id)) {
            set_flash_message("Pengguna berhasil dihapus.", "success");
        } else {
            set_flash_message("Error menghapus pengguna.", "error");
        }
        redirect_to(dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/admin/users');
    }
}
?>