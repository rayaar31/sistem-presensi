<?php
session_start();
include('db.php');

// Periksa apakah pengguna adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

// Proses form untuk membuat atau memperbarui jadwal
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_ibadah = $_POST['nama_ibadah'];
    $tanggal = $_POST['tanggal'];
    $waktu_mulai = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];
    $lokasi = $_POST['lokasi'];
    $musik = $_POST['musik'];
    $multimedia = $_POST['multimedia'];

    if (isset($_POST['jadwal_id'])) { 
        $jadwal_id = $_POST['jadwal_id'];
        $query = "UPDATE jadwal_ibadah SET 
                  nama_ibadah = ?, tanggal = ?, waktu_mulai = ?, waktu_selesai = ?, lokasi = ?, musik = ?, multimedia = ? 
                  WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssi", $nama_ibadah, $tanggal, $waktu_mulai, $waktu_selesai, $lokasi, $musik, $multimedia, $jadwal_id);
    } else {
        $query = "INSERT INTO jadwal_ibadah (nama_ibadah, tanggal, waktu_mulai, waktu_selesai, lokasi, musik, multimedia) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssss", $nama_ibadah, $tanggal, $waktu_mulai, $waktu_selesai, $lokasi, $musik, $multimedia);
    }

    if ($stmt->execute()) {
        header("Location: kelola_jadwal_ibadah.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

// Proses penghapusan jadwal
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = "DELETE FROM jadwal_ibadah WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: kelola_jadwal_ibadah.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

// Ambil data filter dari request
$filter_tanggal = isset($_GET['filter_tanggal']) ? $_GET['filter_tanggal'] : '';
$filter_lokasi = isset($_GET['filter_lokasi']) ? $_GET['filter_lokasi'] : '';

// Query dasar untuk mengambil semua jadwal
$query = "SELECT * FROM jadwal_ibadah WHERE 1=1";

if (!empty($filter_tanggal)) {
    $query .= " AND tanggal = '$filter_tanggal'";
}
if (!empty($filter_lokasi)) {
    $query .= " AND lokasi LIKE '%$filter_lokasi%'";
}

$query .= " ORDER BY tanggal ASC, waktu_mulai ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal Ibadah</title>
    <link rel="stylesheet" href="assets/css/admin/kelola_jadwal_ibadah.css">
</head>
<body>
    <div class="container">
        <a href="admin_dashboard.php" class="back-button">Kembali ke Dashboard</a>
        
        <header>
            <h2>Kelola Jadwal Ibadah</h2>
        </header>

        <section>
            <form action="kelola_jadwal_ibadah.php" method="post">
                <?php if (isset($_GET['edit_id'])): ?>
                    <?php
                    $edit_id = $_GET['edit_id'];
                    $editQuery = "SELECT * FROM jadwal_ibadah WHERE id = ?";
                    $stmt = $conn->prepare($editQuery);
                    $stmt->bind_param("i", $edit_id);
                    $stmt->execute();
                    $editResult = $stmt->get_result();
                    $editData = $editResult->fetch_assoc();
                    ?>
                    <input type="hidden" name="jadwal_id" value="<?= $editData['id'] ?>">
                    <h3>Edit Jadwal Ibadah</h3>
                <?php else: ?>
                    <h3>Tambah Jadwal Ibadah</h3>
                <?php endif; ?>

                <label>Nama Ibadah:</label>
                <input type="text" name="nama_ibadah" value="<?= $editData['nama_ibadah'] ?? '' ?>" required>
                
                <label>Tanggal:</label>
                <input type="date" name="tanggal" value="<?= $editData['tanggal'] ?? '' ?>" required>
                
                <label>Waktu Mulai:</label>
                <input type="time" name="waktu_mulai" value="<?= $editData['waktu_mulai'] ?? '' ?>" required>
                
                <label>Waktu Selesai:</label>
                <input type="time" name="waktu_selesai" value="<?= $editData['waktu_selesai'] ?? '' ?>" required>
                
                <label>Lokasi:</label>
                <input type="text" name="lokasi" value="<?= $editData['lokasi'] ?? '' ?>" required>
                
                <label>Musik (Nama Tim):</label>
                <input type="text" name="musik" value="<?= $editData['musik'] ?? '' ?>" required>
                
                <label>Multimedia (Nama Tim):</label>
                <input type="text" name="multimedia" value="<?= $editData['multimedia'] ?? '' ?>" required>
                
                <button type="submit"><?= isset($editData) ? 'Update Jadwal' : 'Buat Jadwal' ?></button>
            </form>
        </section>

        <section class="filter-section">
            <h3>Filter Jadwal</h3>
            <form method="get" action="kelola_jadwal_ibadah.php">
                <label for="filter_tanggal">Tanggal:</label>
                <input type="date" id="filter_tanggal" name="filter_tanggal" value="<?= $filter_tanggal ?>">

                <label for="filter_lokasi">Lokasi:</label>
                <input type="text" id="filter_lokasi" name="filter_lokasi" placeholder="Cari lokasi..." value="<?= $filter_lokasi ?>">

                <button type="submit">Filter</button>
                <button type="button" class="reset-button" onclick="window.location.href='kelola_jadwal_ibadah.php'">Reset</button>
            </form>
        </section>

        <section>
            <h3>Daftar Jadwal Ibadah</h3>
            <table>
                <tr>
                    <th>Nama Ibadah</th>
                    <th>Tanggal</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Lokasi</th>
                    <th>Musik</th>
                    <th>Multimedia</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['nama_ibadah'] ?></td>
                        <td><?= $row['tanggal'] ?></td>
                        <td><?= $row['waktu_mulai'] ?></td>
                        <td><?= $row['waktu_selesai'] ?></td>
                        <td><?= $row['lokasi'] ?></td>
                        <td><?= $row['musik'] ?></td>
                        <td><?= $row['multimedia'] ?></td>
                        <td class="actions">
                            <a href="kelola_jadwal_ibadah.php?edit_id=<?= $row['id'] ?>">Edit</a>
                            <a href="kelola_jadwal_ibadah.php?delete_id=<?= $row['id'] ?>" class="delete-link" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </section>
    </div>
</body>
</html>
