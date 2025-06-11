<?php
// UAP_WEB/views/dashboard.php

$script_name = dirname($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sintory - <?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $script_name; ?>/assets/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo-area">
                <h1 class="app-title"><a href="<?php echo $script_name; ?>/">Sintory</a></h1>
            </div>
            <nav class="main-nav">
                <a href="<?php echo $script_name; ?>/" class="main-nav-link">Beranda</a>
                <a href="<?php echo $script_name; ?>/admin/products" class="main-nav-link">Barang</a>
                <a href="<?php echo $script_name; ?>/admin/categories" class="main-nav-link">Kategori</a>
                <a href="<?php echo $script_name; ?>/pemasok/suppliers" class="main-nav-link">Pemasok</a>
                <a href="<?php echo $script_name; ?>/admin/transactions" class="main-nav-link">Transaksi</a>
            </nav>
            <div class="user-area">
                <div class="dropdown">
                    <a href="#" class="header-link dropdown-toggle"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?> <i class="fas fa-caret-down"></i></a>
                    <div class="dropdown-menu">
                        <?php if (is_user_admin()): ?>
                            <a href="<?php echo $script_name; ?>/admin/users">Kelola Pengguna</a>
                        <?php endif; ?>
                        <a href="<?php echo $script_name; ?>/logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="content-header">
            <h2><?php echo $page_title; ?></h2>
            <div class="breadcrumbs">
                <i class="fas fa-home"></i> <a href="<?php echo $script_name; ?>/">Home</a> > <?php echo $page_title; ?>
            </div>
        </div>

        <?php
        foreach ($messages as $msg) {
            echo '<div class="alert ' . $msg['type'] . '">' . $msg['text'] . '</div>';
        }
        ?>

        <div class="row">
            <div class="column">
                <div class="card">
                    <h3>Total Barang</h3>
                    <p><h1><?php echo $total_products; ?></h1></p>
                </div>
            </div>
            <div class="column">
                <div class="card">
                    <h3>Total Pemasok</h3>
                    <p><h1><?php echo $total_suppliers; ?></h1></p>
                </div>
            </div>
            <div class="column">
                <div class="card">
                    <h3>Transaksi Hari Ini</h3>
                    <p><h1><?php echo $total_transactions_today; ?></h1></p>
                </div>
            </div>
        </div>
    </div>

    <footer class="app-footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Sintory - Sistem Inventory</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggle = document.querySelector('.dropdown-toggle');
            if (dropdownToggle) {
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    this.closest('.dropdown').querySelector('.dropdown-menu').classList.toggle('show');
                });
            }

            window.addEventListener('click', function(e) {
                if (!e.target.matches('.dropdown-toggle') && !e.target.closest('.dropdown-menu')) {
                    const dropdowns = document.querySelectorAll('.dropdown-menu.show');
                    dropdowns.forEach(function(el) {
                        el.classList.remove('show');
                    });
                }
            });
        });
    </script>
</body>
</html>