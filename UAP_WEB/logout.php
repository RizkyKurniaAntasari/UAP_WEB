<?php
// UAP_WEB/logout.php

session_start();
session_unset();
session_destroy();
require_once 'src/functions.php';

$script_name = dirname($_SERVER['SCRIPT_NAME']);

redirect_to($script_name . '/login.php');
exit();
?>