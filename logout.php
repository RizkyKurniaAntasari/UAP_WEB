<?php
session_start(); // Start the session if not already started (needed for session_destroy to work reliably)
session_destroy();
header("Location: login.php"); // Redirect to the login page
exit;
?>