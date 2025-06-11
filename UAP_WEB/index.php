<?php
// UAP_WEB/index.php

session_start();

require_once __DIR__ . '/src/db.php';
require_once __DIR__ . '/src/functions.php';

// Load all controllers
require_once __DIR__ . '/controllers/admin/Product_Controller.php';
require_once __DIR__ . '/controllers/admin/Category_Controller.php';
require_once __DIR__ . '/controllers/admin/Transaction_Controller.php';
require_once __DIR__ . '/controllers/admin/User_Controller.php';
require_once __DIR__ . '/controllers/pemasok/Supplier_Controller.php';

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$script_name = dirname($_SERVER['SCRIPT_NAME']);

if (strpos($request_uri, $script_name) === 0) {
    $route = substr($request_uri, strlen($script_name));
} else {
    $route = $request_uri;
}

$route = trim($route, '/');
$segments = explode('/', $route);

$controller_name = 'dashboard';
$action_name = 'index';
$id = 0;
$module = '';

if (isset($segments[0]) && $segments[0] !== '') {
    if (in_array($segments[0], ['admin', 'pemasok'])) {
        $module = $segments[0];
        array_shift($segments);
    }

    if (isset($segments[0]) && $segments[0] !== '') {
        $controller_name = $segments[0];
        if (isset($segments[1]) && $segments[1] !== '') {
            $action_name = $segments[1];
            if (isset($segments[2]) && $segments[2] !== '') {
                $id = (int)$segments[2];
            }
        }
    }
}

$render_data = [];

if ($controller_name === 'dashboard' || empty($controller_name)) {
    if (!is_user_logged_in()) {
        redirect_to($script_name . '/login.php');
    } else {
        $page_title = "Dashboard";
        $total_products = Product_Model::get_product_counts($conn);
        $total_suppliers = Supplier_Model::get_supplier_counts($conn);
        $total_transactions_today = Transaction_Model::get_total_transactions_today($conn);
        $messages = get_flash_messages();

        require_once __DIR__ . '/views/dashboard.php';
        close_db_connection($conn);
        exit();
    }
}

$controller_class_name = ucfirst($controller_name) . '_Controller';
$controller_file = __DIR__ . "/controllers/{$module}/" . ucfirst($controller_name) . "_Controller.php";

if (file_exists($controller_file) && class_exists($controller_class_name)) {
    $controller_instance = new $controller_class_name($conn);

    if (!is_user_logged_in()) {
        set_flash_message("Silakan login untuk mengakses halaman ini.", "error");
        redirect_to($script_name . '/login.php');
        exit();
    }
    if ($module === 'admin' && !is_user_admin()) {
        set_flash_message("Akses ditolak. Anda tidak memiliki izin administrator.", "error");
        redirect_to($script_name . '/');
        exit();
    }

    if (method_exists($controller_instance, $action_name)) {
        $render_data = $controller_instance->$action_name($id);
        extract($render_data['data']);
        require_once __DIR__ . '/views/' . $render_data['view'];
    } else {
        set_flash_message("Aksi '$action_name' tidak ditemukan untuk controller '$controller_name'.", "error");
        redirect_to($script_name . '/');
    }
} else {
    set_flash_message("Halaman tidak ditemukan.", "error");
    redirect_to($script_name . '/');
}

close_db_connection($conn);
?>