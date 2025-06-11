<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .error-message {
            color: #dc2626; /* red-700 */
            background-color: #fef2f2; /* red-100 */
            border: 1px solid #ef4444; /* red-400 */
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen font-sans">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-4xl font-bold text-center text-gray-800 mb-8">Masuk ke Akun Anda</h2>
        <form id="loginForm" onsubmit="handleLogin(event)">
            <div id="errorMessage" class="error-message hidden"></div>

            <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="Masukkan email Anda" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="Masukkan password Anda" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300 shadow-md">Login</button>
        </form>
        <p class="text-center text-gray-600 text-sm mt-6">
            Belum punya akun?
            <a href="register.php" class="text-blue-600 hover:underline font-semibold">Daftar sekarang</a>
        </p>
        <p class="text-center text-gray-600 text-sm mt-3">
            <a href="index.php" class="text-blue-600 hover:underline">Kembali ke Beranda</a>
        </p>
    </div>
    <footer class="bg-gray-800 text-white py-4 text-center absolute bottom-0 w-full">
        <div class="container mx-auto px-6">
            <p class="text-sm">&copy; 2025 Sistem Inventory. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script>
        // Data pengguna dummy
        const users = [
            { email: 'admin@example.com', password: 'admin123', role: 'admin' },
            { email: 'pemasok@example.com', password: 'pemasok123', role: 'pemasok' }
        ];

        function handleLogin(event) {
            event.preventDefault(); // Mencegah form melakukan submit default (reload halaman)

            const emailInput = document.getElementById('email').value;
            const passwordInput = document.getElementById('password').value;
            const errorMessageDiv = document.getElementById('errorMessage');

            // Sembunyikan pesan error sebelumnya
            errorMessageDiv.classList.add('hidden');
            errorMessageDiv.textContent = '';

            // Cari pengguna di data dummy
            const foundUser = users.find(user => user.email === emailInput && user.password === passwordInput);

            if (foundUser) {
                // Login berhasil
                // Simpan role di localStorage (simulasi sesi di browser)
                localStorage.setItem('userRole', foundUser.role);
                localStorage.setItem('userEmail', foundUser.email);
                // Opsional: simpan username juga, jika diperlukan di dashboard (misal untuk "Selamat Datang, [username]!")
                localStorage.setItem('userUsername', foundUser.email.split('@')[0]); // Contoh sederhana ambil sebelum '@'

                // Redirect berdasarkan role
                if (foundUser.role === 'admin') {
                    window.location.href = 'views/admin/dashboard.php';
                } else if (foundUser.role === 'pemasok') {
                    window.location.href = 'views/pemasok/dashboard.php';
                }
            } else {
                // Login gagal
                errorMessageDiv.textContent = 'Email atau password salah.';
                errorMessageDiv.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>