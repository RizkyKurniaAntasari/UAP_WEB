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
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Sekarang ini adalah warna utama aplikasi Anda
                        'primary-app-color': '#1976d2',
                        'primary-app-color-dark': '#1565c0',
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $script_name; ?>/assets/css/style.css">
</head>
<body class="font-poppins text-gray-800 bg-gray-50 flex flex-col min-h-screen">
    <header class="w-full bg-primary-app-color shadow-md py-4 px-5 box-border">
        <div class="max-w-7xl mx-auto flex justify-between items-center text-white flex-wrap gap-5">
            <div class="flex items-center">
                <h1 class="text-xl font-semibold whitespace-nowrap"><a href="<?php echo $script_name; ?>/" class="text-white no-underline">Sintory</a></h1>
            </div>
            <nav class="flex items-center gap-5 flex-wrap flex-grow justify-start">
                <a href="<?php echo $script_name; ?>/" class="text-white no-underline font-medium hover:opacity-80 whitespace-nowrap">Beranda</a>
                <a href="<?php echo $script_name; ?>/admin/products" class="text-white no-underline font-medium hover:opacity-80 whitespace-nowrap">Barang</a>
                <a href="<?php echo $script_name; ?>/admin/categories" class="text-white no-underline font-medium hover:opacity-80 whitespace-nowrap">Kategori</a>
                <a href="<?php echo $script_name; ?>/pemasok/suppliers" class="text-white no-underline font-medium hover:opacity-80 whitespace-nowrap">Pemasok</a>
                <a href="<?php echo $script_name; ?>/admin/transactions" class="text-white no-underline font-medium hover:opacity-80 whitespace-nowrap">Transaksi</a>
            </nav>
            <div class="flex items-center gap-4">
                <div class="relative inline-block dropdown">
                    <a href="#" class="flex items-center text-white no-underline font-normal hover:opacity-80 px-2 py-1 rounded cursor-pointer whitespace-nowrap transition duration-200 dropdown-toggle">
                        <i class="fas fa-user-circle mr-2 text-lg"></i> <?php echo htmlspecialchars($_SESSION['username']); ?> <i class="fas fa-caret-down ml-2 text-sm"></i>
                    </a>
                    <div class="hidden absolute bg-white min-w-[160px] shadow-lg rounded-md overflow-hidden z-50 right-0 top-full dropdown-menu">
                        <?php if (is_user_admin()): ?>
                            <a href="<?php echo $script_name; ?>/admin/users" class="block px-4 py-3 text-gray-700 no-underline whitespace-nowrap hover:bg-gray-100 transition duration-200">Kelola Pengguna</a>
                        <?php endif; ?>
                        <a href="<?php echo $script_name; ?>/logout.php" class="block px-4 py-3 text-gray-700 no-underline whitespace-nowrap hover:bg-gray-100 transition duration-200">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto my-8 p-6 bg-white rounded-lg shadow-md flex-1">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-3xl font-semibold text-gray-700">Dashboard</h2>
            <div class="text-sm text-gray-600">
                <i class="fas fa-home mr-1 text-gray-400"></i> <a href="<?php echo $script_name; ?>/" class="text-gray-600 no-underline hover:underline">Home</a> > Dashboard
            </div>
        </div>

        <?php
        foreach ($messages as $msg) {
            $alert_class = ($msg['type'] === 'success') ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200';
            echo '<div class="mb-4 p-3 rounded-md text-sm border ' . $alert_class . '">' . $msg['text'] . '</div>';
        }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            <div class="bg-gray-50 border border-gray-200 p-6 rounded-lg shadow-sm text-center transform hover:-translate-y-1 transition duration-200 h-full flex flex-col justify-center">
                <h3 class="text-base font-semibold text-gray-600 mb-4">Total Barang</h3>
                <p><h1 class="text-primary-app-color text-6xl font-bold leading-none"><?php echo $total_products; ?></h1></p>
            </div>
            <div class="bg-gray-50 border border-gray-200 p-6 rounded-lg shadow-sm text-center transform hover:-translate-y-1 transition duration-200 h-full flex flex-col justify-center">
                <h3 class="text-base font-semibold text-gray-600 mb-4">Total Pemasok</h3>
                <p><h1 class="text-primary-app-color text-6xl font-bold leading-none"><?php echo $total_suppliers; ?></h1></p>
            </div>
            <div class="bg-gray-50 border border-gray-200 p-6 rounded-lg shadow-sm text-center transform hover:-translate-y-1 transition duration-200 h-full flex flex-col justify-center">
                <h3 class="text-base font-semibold text-gray-600 mb-4">Transaksi Hari Ini</h3>
                <p><h1 class="text-primary-app-color text-6xl font-bold leading-none"><?php echo $total_transactions_today; ?></h1></p>
            </div>
        </div>

    </div>

    <footer class="w-full bg-primary-app-color text-white py-4 mt-8 shadow-inner box-border">
        <div class="max-w-4xl mx-auto px-5 flex justify-center items-center flex-wrap">
            <p class="text-sm opacity-90 text-center w-full">&copy; <?php echo date('Y'); ?> Sintory - Sistem Inventory</p>
        </div>
    </footer>

    <script>
        // Dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggle = document.querySelector('.dropdown-toggle');
            if (dropdownToggle) {
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    this.closest('.dropdown').querySelector('.dropdown-menu').classList.toggle('hidden');
                });
            }

            // Close dropdown if clicked outside
            window.addEventListener('click', function(e) {
                if (!e.target.matches('.dropdown-toggle') && !e.target.closest('.dropdown-menu')) {
                    const dropdowns = document.querySelectorAll('.dropdown-menu:not(.hidden)');
                    dropdowns.forEach(function(el) {
                        el.classList.add('hidden');
                    });
                }
            });
        });
    </script>
</body>
</html>