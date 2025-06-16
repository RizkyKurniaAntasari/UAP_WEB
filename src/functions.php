<?php
define('BASE_URL', '/' . basename(dirname(__DIR__))); // misal: '/inventaris'
password_hash("admin123", PASSWORD_DEFAULT);

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function redirect_views_admin($url) {
    header("Location: /views/admin" . $url);
    exit;
}

function redirect_views_pemasok($url) {
    header("Location: /views/pemasok" . $url);
    exit;
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
?>