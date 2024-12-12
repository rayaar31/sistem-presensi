<?php
session_start();
include('db.php');

// Pastikan pengguna login dan bukan admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 1) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil status presensi pengguna
$query = "SELECT status_presensi_masuk, status_presensi_keluar FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "Pengguna tidak ditemukan.";
    exit();
}

$status_masuk = $user['status_presensi_masuk'];
$status_keluar = $user['status_presensi_keluar'];
$message = "";

// Proses presensi jika valid
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipe_presensi = $_POST['tipe_presensi'];
    $waktu_presensi = date("Y-m-d H:i:s");

    // Cek urutan presensi
    $query_last = "SELECT tipe_presensi FROM presensi WHERE user_id = ? ORDER BY waktu_presensi DESC LIMIT 1";
    $stmt_last = $conn->prepare($query_last);
    $stmt_last->bind_param("i", $user_id);
    $stmt_last->execute();
    $result_last = $stmt_last->get_result();
    $last_presensi = $result_last->fetch_assoc();

    if ($tipe_presensi == "Keluar" && (!$last_presensi || $last_presensi['tipe_presensi'] != "Masuk")) {
        $message = "Anda harus presensi masuk terlebih dahulu sebelum presensi keluar.";
    } elseif ($last_presensi && $last_presensi['tipe_presensi'] == $tipe_presensi) {
        $message = "Anda sudah melakukan presensi $tipe_presensi.";
    } else {
        // Catat presensi
        $query_insert = "INSERT INTO presensi (user_id, tipe_presensi, waktu_presensi) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($query_insert);
        $stmt_insert->bind_param("iss", $user_id, $tipe_presensi, $waktu_presensi);

        if ($stmt_insert->execute()) {
            $message = "Presensi $tipe_presensi berhasil dicatat pada $waktu_presensi.";
        } else {
            $message = "Gagal mencatat presensi. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Presensi</title>
    <link rel="stylesheet" href="assets/css/user/presensi.css">
</head>
<body>
    <h2>Halaman Presensi</h2>

    <?php if ($status_masuk == 1 || $status_keluar == 1): ?>
        <form action="presensi.php" method="post">
            <label for="tipe_presensi">Tipe Presensi:</label>
            <select name="tipe_presensi" id="tipe_presensi" required>
                <?php if ($status_masuk == 1): ?>
                    <option value="Masuk">Masuk</option>
                <?php endif; ?>
                <?php if ($status_keluar == 1): ?>
                    <option value="Keluar">Keluar</option>
                <?php endif; ?>
            </select>
            <button type="submit">Lakukan Presensi</button>
        </form>
    <?php else: ?>
        <p>Presensi tidak aktif untuk Anda saat ini.</p>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    
    <br>
    <a href="user_dashboard.php">
        <button type="button">Kembali ke Dashboard</button>
    </a>
</body>
</html>
