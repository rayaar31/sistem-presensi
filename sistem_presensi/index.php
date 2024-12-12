<?php
session_start();
include('db.php'); // Menghubungkan ke database

// Memproses form login saat dikirimkan
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mencari pengguna dengan username dan password yang cocok
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Set session dan arahkan ke halaman berdasarkan role
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role_id'];

        if ($user['role_id'] == 1) {
            header("Location: admin_dashboard.php");  // Arahkan ke halaman admin
        } else {
            header("Location: user_dashboard.php");   // Arahkan ke halaman user
        }
        exit();
    } else {
        $error = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Presensi</title>
    <link rel="stylesheet" href="assets/css/index.css"> <!-- Link ke file CSS -->
</head>
<body>
    <div class="login-container">
        <header>
            <h1>Sistem Presensi</h1>
            <p>Silakan login untuk melanjutkan.</p>
        </header>

        <form action="" method="post" class="login-form">
            <input type="text" name="username" placeholder="Username" required>
            
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="button" id="togglePassword">Show</button>
            </div>

            <button type="submit">Login</button>
        </form>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>

    <script>
        // JavaScript untuk toggle show/hide password
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('togglePassword');

        togglePasswordButton.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Show' : 'Hide';
        });
    </script>
</body>
</html>


