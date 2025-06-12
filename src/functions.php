<?php
function redirect($url) {
    header("location: " . $url);
    exit;
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
?>