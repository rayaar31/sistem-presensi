<?php
session_start();
include('db.php');

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: index.php");
    exit();
}

// Ambil tanggal hari ini
$tanggalHariIni = date('Y-m-d');

// Query untuk mengambil jadwal mulai dari hari ini ke depan
$sql = "SELECT * FROM jadwal_ibadah WHERE tanggal >= '$tanggalHariIni' ORDER BY tanggal ASC";
$jadwalResult = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lihat Jadwal Ibadah - User</title>
    <link rel="stylesheet" href="assets/css/user/lihat_jadwal.css"> <!-- Link ke file CSS -->
</head>
<body>
    <!-- Tombol kembali ke dashboard -->
    <a href="user_dashboard.php" class="back-to-dashboard">Kembali ke Dashboard</a>

    <div class="container">
        <h2>Jadwal Ibadah</h2>

        <?php if ($jadwalResult->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Nama Ibadah</th>
                    <th>Tanggal</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Lokasi</th>
                    <th>Pengguna Musik</th>
                    <th>Pengguna Multimedia</th>
                </tr>
                <?php while ($row = $jadwalResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['nama_ibadah']; ?></td>
                        <td><?php echo $row['tanggal']; ?></td>
                        <td><?php echo $row['waktu_mulai']; ?></td>
                        <td><?php echo $row['waktu_selesai']; ?></td>
                        <td><?php echo $row['lokasi']; ?></td>
                        <td><?php echo $row['musik']; ?></td>
                        <td><?php echo $row['multimedia']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Tidak ada jadwal ibadah untuk hari ini.</p>
        <?php endif; ?>
    </div>
</body>
</html>
