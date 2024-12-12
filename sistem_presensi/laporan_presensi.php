<?php
session_start();
include('db.php'); // Koneksi ke database

// Periksa apakah pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Filter data berdasarkan username
$usernameFilter = '';
if (isset($_GET['username']) && $_GET['username'] !== '') {
    $usernameFilter = $conn->real_escape_string($_GET['username']);
}

// Query untuk mendapatkan daftar username
$usernames = $conn->query("SELECT DISTINCT username FROM users");

// Query untuk mendapatkan data presensi 
$sql = "
    SELECT 
        p.id, 
        u.username, 
        p.tipe_presensi, 
        p.waktu_presensi
    FROM presensi p
    INNER JOIN users u ON p.user_id = u.id
";
if ($usernameFilter !== '') {
    $sql .= " WHERE u.username = '$usernameFilter'";
}
$sql .= " ORDER BY p.waktu_presensi DESC";

$presensi = $conn->query($sql);

// Persiapkan data untuk ringkasan total presensi masuk per pengguna
$totalPresensiQuery = "
    SELECT 
        u.username, 
        COUNT(p.id) AS total_presensi_masuk 
    FROM users u
    LEFT JOIN presensi p ON u.id = p.user_id AND p.tipe_presensi = 'Masuk'
";
if ($usernameFilter !== '') {
    $totalPresensiQuery .= " WHERE u.username = '$usernameFilter'";
}
$totalPresensiQuery .= " GROUP BY u.username ORDER BY total_presensi_masuk DESC";
$totalPresensiResult = $conn->query($totalPresensiQuery);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi</title>
    <link rel="stylesheet" href="assets/css/admin/laporan_presensi.css">
</head>
<body>
    <!-- Tombol Kembali ke Dashboard -->
    <a href="admin_dashboard.php" class="back-link">Kembali</a>

    <div class="container">
        <h2>Laporan Presensi</h2>

        <!-- Filter Data -->
        <h3>Filter Presensi</h3>
        <form method="GET">
            <label for="username">Username:</label>
            <select name="username" id="username">
                <option value="">Semua</option>
                <?php 
                // Reset pointer untuk username
                $usernames->data_seek(0);
                while ($row = $usernames->fetch_assoc()): ?>
                    <option value="<?php echo $row['username']; ?>" <?php echo ($row['username'] === $usernameFilter) ? 'selected' : ''; ?>>
                        <?php echo $row['username']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Filter</button>
        </form>

        <!-- Tabel Laporan Presensi -->
        <h3>Data Presensi</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Tipe Presensi</th>
                    <th>Waktu Presensi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($presensi->num_rows > 0): ?>
                    <?php while ($row = $presensi->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['tipe_presensi']; ?></td>
                            <td><?php echo $row['waktu_presensi']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Tidak ada data presensi yang ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Tabel Total Presensi Masuk -->
        <h3>Total Presensi Masuk per Pengguna</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th class="total-presensi-header">Total Presensi Masuk</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalPresensiResult->num_rows > 0): ?>
                    <?php while ($row = $totalPresensiResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['username']; ?></td>
                            <td class="total-presensi"><?php echo $row['total_presensi_masuk']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">Tidak ada data total presensi masuk yang ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>