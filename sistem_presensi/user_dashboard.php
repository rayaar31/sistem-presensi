<?php
session_start();
include('db.php');

// Periksa apakah pengguna telah login dan memiliki role user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html> 
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="assets/css/user/user_dashboard.css">
</head>
<body>
    <div class="container">
        <h2>Selamat datang, <?php echo $_SESSION['username']; ?>!</h2>
        
        <nav class="user-menu">
            <a href="lihat_jadwal.php">Lihat Jadwal Ibadah</a>
            <a href="presensi.php">Lakukan Presensi</a>
        </nav>
        
        <div class="logout-container">
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</body>
</html>
