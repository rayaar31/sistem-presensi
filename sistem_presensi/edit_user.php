<?php
session_start();
include('db.php'); // Koneksi ke database

// Periksa apakah pengguna adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

// Fungsi untuk mengambil semua pengguna dari database
function getAllUsers($conn) {
    $sql = "SELECT id, username, role_id, tugas FROM users";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Menambahkan pengguna baru
if (isset($_POST['add_user'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role_id = intval($_POST['role_id']);
    $tugas = $conn->real_escape_string($_POST['tugas']);

    $sql = "INSERT INTO users (username, password, role_id, tugas) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $username, $password, $role_id, $tugas);
    if ($stmt->execute()) {
        $message = "Pengguna berhasil ditambahkan.";
    } else {
        $message = "Gagal menambahkan pengguna: " . $conn->error;
    }
}

// Menghapus pengguna
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']); // Pastikan $id adalah integer untuk keamanan
    // Mulai transaksi untuk memastikan konsistensi
    $conn->begin_transaction();
    try {
        // Hapus data terkait di tabel presensi
        $deletePresensi = "DELETE FROM presensi WHERE user_id = ?";
        $stmtPresensi = $conn->prepare($deletePresensi);
        $stmtPresensi->bind_param("i", $id);
        $stmtPresensi->execute();

        // Hapus pengguna dari tabel users
        $deleteUser = "DELETE FROM users WHERE id = ?";
        $stmtUser = $conn->prepare($deleteUser);
        $stmtUser->bind_param("i", $id);
        $stmtUser->execute();

        // Commit transaksi
        $conn->commit();
        $message = "Pengguna dan data presensi terkait berhasil dihapus.";
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        $conn->rollback();
        $message = "Gagal menghapus pengguna: " . $e->getMessage();
    }
}

// Mengubah password pengguna
if (isset($_POST['update_password'])) {
    $id = intval($_POST['user_id']);
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_password, $id);
    if ($stmt->execute()) {
        $message = "Password berhasil diubah.";
    } else {
        $message = "Gagal mengubah password: " . $conn->error;
    }
}

$users = getAllUsers($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="assets/css/admin/edit_user.css"> <!-- Link ke file CSS -->
</head>
<body>
    <div class="container">
        <!-- Tombol Kembali ke Dashboard -->
        <a href="admin_dashboard.php" class="back-link">Kembali</a>

        <h2>Kelola Pengguna</h2>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <!-- Form Tambah Pengguna -->
        <h3>Tambah Pengguna Baru</h3>
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>
            <br>
            <label>Password:</label>
            <input type="password" name="password" required>
            <br>
            <label>Role:</label>
            <select name="role_id" required>
                <option value="1">Admin</option>
                <option value="2">User</option>
            </select>
            <br>
            <label>Tugas:</label>
            <input type="text" name="tugas" required>
            <br>
            <button type="submit" name="add_user">Tambah Pengguna</button>
        </form>

        <!-- Daftar Pengguna -->
        <h3>Daftar Pengguna</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Tugas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo $user['role_id'] == 1 ? 'Admin' : 'User'; ?></td>
                        <td><?php echo htmlspecialchars($user['tugas']); ?></td>
                        <td>
                            <div class="action-container">
                                <!-- Tombol Hapus -->
                                <a href="?delete_user=<?php echo htmlspecialchars($user['id']); ?>" 
                                   class="delete" 
                                   onclick="return confirm('Yakin ingin menghapus pengguna ini?');">Hapus</a>

                                <!-- Form Ubah Password -->
                                <form method="POST">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                    <input type="password" name="new_password" placeholder="Password Baru" required>
                                    <button type="submit" name="update_password">Ubah Password</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

