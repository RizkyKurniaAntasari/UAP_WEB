function logoutClientSide(event) {
            event.preventDefault(); // Mencegah navigasi default
            localStorage.removeItem('userRole');
            localStorage.removeItem('userEmail');
            window.location.href = '../../logout.php'; // Path ke logout.php di root
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('userRole') !== 'admin') {
                window.location.href = '../../index.php'; // Path ke index.php di root
            }
        });