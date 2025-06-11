<?php
// UAP_WEB/src/functions.php

function redirect_to($url) {
    header("Location: " . $url);
    exit();
}

function is_user_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_user_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function hash_user_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_user_password($password, $hashed_password) {
    return password_verify($password, $hashed_password);
}

function sanitize_user_input($data) {
    global $conn;
    if ($conn && is_string($data)) {
        $data = $conn->real_escape_string($data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function set_flash_message($message, $type = 'success') {
    if (!isset($_SESSION['messages'])) {
        $_SESSION['messages'] = [];
    }
    $_SESSION['messages'][] = ['text' => $message, 'type' => $type];
}

function get_flash_messages() {
    if (isset($_SESSION['messages'])) {
        $messages = $_SESSION['messages'];
        unset($_SESSION['messages']);
        return $messages;
    }
    return [];
}

class Product_Model {
    public static function get_all_products($conn) {
        $sql = "SELECT p.id, p.name, c.name AS category_name, s.name AS supplier_name, p.stock, p.price
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                ORDER BY p.name ASC";
        $result = $conn->query($sql);
        $products = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        return $products;
    }

    public static function get_product_by_id($conn, $id) {
        $stmt = $conn->prepare("SELECT id, name, category_id, supplier_id, stock, price, description FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        return $product;
    }

    public static function create_product($conn, $data) {
        $stmt = $conn->prepare("INSERT INTO products (name, category_id, supplier_id, stock, price, description) VALUES (?, ?, ?, ?, ?, ?)");
        $category_id = empty($data['category_id']) ? null : (int)$data['category_id'];
        $supplier_id = empty($data['supplier_id']) ? null : (int)$data['supplier_id'];

        $stmt->bind_param("siiids",
            $data['name'],
            $category_id,
            $supplier_id,
            $data['stock'],
            $data['price'],
            $data['description']
        );
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public static function update_product($conn, $id, $data) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, category_id = ?, supplier_id = ?, stock = ?, price = ?, description = ? WHERE id = ?");
        $category_id = empty($data['category_id']) ? null : (int)$data['category_id'];
        $supplier_id = empty($data['supplier_id']) ? null : (int)$data['supplier_id'];

        $stmt->bind_param("siiidsi",
            $data['name'],
            $category_id,
            $supplier_id,
            $data['stock'],
            $data['price'],
            $data['description'],
            $id
        );
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public static function delete_product($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public static function get_product_counts($conn) {
        $sql = "SELECT COUNT(*) AS total FROM products";
        $result = $conn->query($sql);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }
}

class Category_Model {
    public static function get_all_categories($conn) {
        $sql = "SELECT id, name, description FROM categories ORDER BY name ASC";
        $result = $conn->query($sql);
        $categories = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        return $categories;
    }

    public static function get_category_by_id($conn, $id) {
        $stmt = $conn->prepare("SELECT id, name, description FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
        $stmt->close();
        return $category;
    }

    public static function create_category($conn, $data) {
        $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $data['name'], $data['description']);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public static function update_category($conn, $id, $data) {
        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $data['name'], $data['description'], $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public static function delete_category($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}

class Supplier_Model {
    public static function get_all_suppliers($conn) {
        $sql = "SELECT id, name, contact_person, phone, email, address FROM suppliers ORDER BY name ASC";
        $result = $conn->query($sql);
        $suppliers = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $suppliers[] = $row;
            }
        }
        return $suppliers;
    }

    public static function get_supplier_by_id($conn, $id) {
        $stmt = $conn->prepare("SELECT id, name, contact_person, phone, email, address FROM suppliers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $supplier = $result->fetch_assoc();
        $stmt->close();
        return $supplier;
    }

    public static function create_supplier($conn, $data) {
        $stmt = $conn->prepare("INSERT INTO suppliers (name, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $data['name'], $data['contact_person'], $data['phone'], $data['email'], $data['address']);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public static function update_supplier($conn, $id, $data) {
        $stmt = $conn->prepare("UPDATE suppliers SET name = ?, contact_person = ?, phone = ?, email = ?, address = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $data['name'], $data['contact_person'], $data['phone'], $data['email'], $data['address'], $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public static function delete_supplier($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public static function get_supplier_counts($conn) {
        $sql = "SELECT COUNT(*) AS total FROM suppliers";
        $result = $conn->query($sql);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }
}

class Transaction_Model {
    public static function get_all_transactions($conn) {
        $sql = "SELECT t.id, p.name AS product_name, t.type, t.quantity, t.transaction_date, u.username AS user_name, t.notes
                FROM transactions t
                JOIN products p ON t.product_id = p.id
                LEFT JOIN users u ON t.user_id = u.id
                ORDER BY t.transaction_date DESC";
        $result = $conn->query($sql);
        $transactions = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $transactions[] = $row;
            }
        }
        return $transactions;
    }

    public static function get_transaction_by_id($conn, $id) {
        $stmt = $conn->prepare("SELECT id, product_id, type, quantity, transaction_date, user_id, notes FROM transactions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $transaction = $result->fetch_assoc();
        $stmt->close();
        return $transaction;
    }

    public static function create_transaction($conn, $data) {
        $conn->begin_transaction();
        try {
            $stmt_trans = $conn->prepare("INSERT INTO transactions (product_id, type, quantity, user_id, notes) VALUES (?, ?, ?, ?, ?)");
            $stmt_trans->bind_param("isiss",
                $data['product_id'],
                $data['type'],
                $data['quantity'],
                $data['user_id'],
                $data['notes']
            );
            if (!$stmt_trans->execute()) {
                throw new Exception("Failed to insert transaction record: " . $stmt_trans->error);
            }
            $stmt_trans->close();

            $stock_sql = "";
            if ($data['type'] === 'in') {
                $stock_sql = "UPDATE products SET stock = stock + ? WHERE id = ?";
            } elseif ($data['type'] === 'out') {
                $stmt_check_stock = $conn->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
                $stmt_check_stock->bind_param("i", $data['product_id']);
                $stmt_check_stock->execute();
                $result_check_stock = $stmt_check_stock->get_result();
                $current_stock = 0;
                if ($row = $result_check_stock->fetch_assoc()) {
                    $current_stock = $row['stock'];
                }
                $stmt_check_stock->close();

                if ($current_stock < $data['quantity']) {
                    throw new Exception("Stok tidak cukup untuk transaksi keluar ini. Tersedia: " . $current_stock);
                }
                $stock_sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
            } else {
                throw new Exception("Tipe transaksi tidak valid.");
            }

            $stmt_stock = $conn->prepare($stock_sql);
            $stmt_stock->bind_param("ii", $data['quantity'], $data['product_id']);
            if (!$stmt_stock->execute()) {
                throw new Exception("Failed to update product stock: " . $stmt_stock->error);
            }
            $stmt_stock->close();

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollback();
            set_flash_message("Transaksi gagal: " . $e->getMessage(), "error");
            return false;
        }
    }

    public static function get_total_transactions_today($conn) {
        $sql = "SELECT COUNT(*) AS total FROM transactions WHERE DATE(transaction_date) = CURDATE()";
        $result = $conn->query($sql);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    public static function get_transactions_report($conn, $start_date, $end_date, $product_id = null, $type = null) {
        $sql = "SELECT t.id, p.name AS product_name, t.type, t.quantity, t.transaction_date, u.username AS user_name, t.notes, p.price
                FROM transactions t
                JOIN products p ON t.product_id = p.id
                LEFT JOIN users u ON t.user_id = u.id
                WHERE t.transaction_date BETWEEN ? AND ? + INTERVAL 1 DAY";
        $params = [$start_date, $end_date];
        $types = "ss";

        if ($product_id) {
            $sql .= " AND t.product_id = ?";
            $params[] = $product_id;
            $types .= "i";
        }
        if ($type && ($type === 'in' || $type === 'out')) {
            $sql .= " AND t.type = ?";
            $params[] = $type;
            $types .= "s";
        }

        $sql .= " ORDER BY t.transaction_date ASC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $report_data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $report_data[] = $row;
            }
        }
        $stmt->close();
        return $report_data;
    }
}

class User_Model {
    public static function get_all_users($conn) {
        $sql = "SELECT id, username, role, created_at FROM users ORDER BY username ASC";
        $result = $conn->query($sql);
        $users = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        return $users;
    }

    public static function get_user_by_id($conn, $id) {
        $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public static function create_user($conn, $data) {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $hashed_password = hash_user_password($data['password']);
        $stmt->bind_param("sss", $data['username'], $hashed_password, $data['role']);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public static function update_user($conn, $id, $data) {
        $sql = "UPDATE users SET username = ?, role = ? ";
        $types = "ss";
        $params = [$data['username'], $data['role']];

        if (!empty($data['password'])) {
            $sql .= ", password = ? ";
            $hashed_password = hash_user_password($data['password']);
            $params[] = $hashed_password;
            $types .= "s";
        }
        $sql .= "WHERE id = ?";
        $params[] = $id;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public static function delete_user($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>