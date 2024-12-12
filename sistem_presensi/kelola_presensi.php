<?php
session_start();
include('db.php');

// Periksa apakah pengguna adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

// Proses untuk mengaktifkan atau menonaktifkan presensi masuk atau keluar
if (isset($_POST['update_presensi']) && isset($_POST['tipe_presensi'])) {
    $user_id = $_POST['user_id'];
    $tipe_presensi = $_POST['tipe_presensi']; // Masuk atau Keluar
    $status_presensi = $_POST['status_presensi']; // 1 untuk aktif, 0 untuk nonaktif

    // Tentukan kolom yang akan diubah berdasarkan tipe presensi
    $column = ($tipe_presensi == "Masuk") ? "status_presensi_masuk" : "status_presensi_keluar";

    // Update status presensi untuk pengguna
    $query = "UPDATE users SET $column = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $status_presensi, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Status presensi $tipe_presensi berhasil diperbarui.');</script>";
    } else {
        echo "<script>alert('Gagal memperbarui status presensi $tipe_presensi.');</script>";
    }
    $stmt->close();
}

// Ambil data semua pengguna
$query_users = "SELECT id, username, tugas, status_presensi_masuk, status_presensi_keluar FROM users WHERE role_id != 1";
$result_users = $conn->query($query_users);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Presensi</title>
    <link rel="stylesheet" href="assets/css/admin/kelola_presensi.css">
</head>
<body>
    <h2>Kelola Presensi</h2>

    <table border="1">
        <tr>
            <th>Username</th>
            <th>Tugas</th>
            <th>Status Presensi Masuk</th>
            <th>Status Presensi Keluar</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $result_users->fetch_assoc()): ?>
            <tr>
                <td><?= $row['username'] ?></td>
                <td><?= $row['tugas'] ?></td>
                <td><?= $row['status_presensi_masuk'] == 1 ? 'Aktif' : 'Nonaktif' ?></td>
                <td><?= $row['status_presensi_keluar'] == 1 ? 'Aktif' : 'Nonaktif' ?></td>
                <td>
                    <!-- Form untuk mengaktifkan/nonaktifkan presensi masuk -->
                    <form method="post" action="kelola_presensi.php" style="display: inline-block;">
                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="tipe_presensi" value="Masuk">
                        <input type="hidden" name="status_presensi" value="<?= $row['status_presensi_masuk'] == 1 ? 0 : 1 ?>">
                        <button type="submit" name="update_presensi">
                            <?= $row['status_presensi_masuk'] == 1 ? 'Nonaktifkan Masuk' : 'Aktifkan Masuk' ?>
                        </button>
                    </form>
                    <form method="post" action="kelola_presensi.php" style="display: inline-block;">
                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="tipe_presensi" value="Keluar">
                        <input type="hidden" name="status_presensi" value="<?= $row['status_presensi_keluar'] == 1 ? 0 : 1 ?>">
                        <button type="submit" name="update_presensi">
                            <?= $row['status_presensi_keluar'] == 1 ? 'Nonaktifkan Keluar' : 'Aktifkan Keluar' ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="admin_dashboard.php">
        <button type="button">Kembali</button>
    </a>
</body>
</html>
