<?php
$pass = "user";
$pass_hash = password_hash($pass, PASSWORD_DEFAULT);
echo "Password hashed: $pass_hash";
?>