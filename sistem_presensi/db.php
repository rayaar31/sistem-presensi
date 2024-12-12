<?php
// Ambil DATABASE_URL dari environment
$dbUrl = getenv('mysql://root:securepassword@containers-us-west-45.railway.app:3306/sistem_presensi');
if (!$dbUrl) {
    die("Error: DATABASE_URL tidak ditemukan. Tambahkan pada Environment Railway.");
}

// Parse DATABASE_URL
$parsedUrl = parse_url($dbUrl);

$db_host = $parsedUrl['host'] ?? 'localhost';
$db_user = $parsedUrl['user'] ?? 'root';
$db_pass = $parsedUrl['pass'] ?? '';
$db_name = ltrim($parsedUrl['path'] ?? '', '/');

// Koneksi ke database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
