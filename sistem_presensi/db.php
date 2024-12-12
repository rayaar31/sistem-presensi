<?php
// Mengambil DATABASE_URL dari environment
$dbUrl = getenv('DATABASE_URL');
if (!$dbUrl) {
    die("Error: DATABASE_URL tidak ditemukan. Tambahkan pada Environment Railway.");
}

// Memecah DATABASE_URL untuk mendapatkan informasi koneksi
$parsedUrl = parse_url($dbUrl);

$db_host = $parsedUrl['host'] ?? 'localhost';
$db_user = $parsedUrl['user'] ?? 'root';
$db_pass = $parsedUrl['pass'] ?? '';
$db_name = ltrim($parsedUrl['path'], '/');

// Koneksi ke database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
