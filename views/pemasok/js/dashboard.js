<?php if (!$isLoggedIn): ?>
    alert("Anda belum login. Silakan login terlebih dahulu.");
    // window.location.href = '../../login.php'; 
    <?php endif; ?>

function logoutClientSide(event) {
            // Ini adalah placeholder, logout sebenarnya akan ditangani oleh logout.php
    console.log("Logging out...");
            // event.preventDefault(); 
            // window.location.href = '../../logout.php'; 
}
    