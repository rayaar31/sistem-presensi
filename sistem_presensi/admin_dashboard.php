<?php
session_start();
include('db.php'); // Koneksi ke database

// Periksa apakah pengguna adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="assets/css/admin/admin_dashboard.css"> <!-- Link ke file CSS -->
</head>
<body>
    <div class="container">
        <header>
            <h1>Dashboard Admin</h1>
            <p>Selamat datang, <?php echo $_SESSION['username']; ?>!</p>
        </header>

        <nav class="admin-menu">
            <a href="kelola_jadwal_ibadah.php">Kelola Jadwal Ibadah</a>
            <a href="kelola_presensi.php">Kelola Presensi</a>
            <a href="edit_user.php">Kelola Pengguna</a>
            <a href="laporan_presensi.php">Laporan Presensi</a>
        </nav>
        <div class="logout-container">
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>
</body>
</html>
