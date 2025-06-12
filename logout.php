<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Sistem Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen font-sans">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Anda telah berhasil logout.</h2>
        <p class="text-gray-600 mb-6">Terima kasih telah menggunakan layanan kami.</p>
        <a href="login.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300">Login Kembali</a>
    </div>

    <script>
        // Hapus data autentikasi dari localStorage saat halaman logout dimuat
        localStorage.removeItem('userRole');
        localStorage.removeItem('userEmail');
        // Tidak ada redirect otomatis di sini, biarkan pengguna melihat pesan logout
        // dan klik tombol untuk login kembali.
    </script>
</body>
</html>
