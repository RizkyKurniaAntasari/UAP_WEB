<?php
$pass = "654321";
$pass_hash = password_hash($pass, PASSWORD_DEFAULT);
echo "Password hashed: $pass_hash\n";
?>