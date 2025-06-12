<?php
define('BASE_URL', '/' . basename(dirname(__DIR__))); // misal: '/inventaris'

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function redirect_views_admin($url) {
    header("Location: " . BASE_URL . "/views/admin" . $url);
    exit;
}

function redirect_views_pemasok($url) {
    header("Location: " . BASE_URL . "/views/pemasok" . $url);
    exit;
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
?>