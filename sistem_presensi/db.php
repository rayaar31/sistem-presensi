<?php
$dbUrl = getenv('DATABASE_URL');
if (!$dbUrl) {
    die("Error: DATABASE_URL tidak ditemukan. Tambahkan pada Environment Railway.");
}

$parsedUrl = parse_url($dbUrl);

// Pastikan semua bagian URL tersedia
if (!isset($parsedUrl['host'], $parsedUrl['user'], $parsedUrl['pass'], $parsedUrl['path'])) {
    die("Error: Konfigurasi DATABASE_URL tidak lengkap.");
}

$db_host = $parsedUrl['host'];
$db_user = $parsedUrl['user'];
$db_pass = $parsedUrl['pass'];
$db_name = ltrim($parsedUrl['path'], '/');

// Mengatur koneksi database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Periksa koneksi
if ($conn->connect_error) {
    if (getenv('APP_ENV') === 'production') {
        die("Koneksi gagal: Terjadi kesalahan pada server.");
    } else {
        die("Koneksi gagal: " . $conn->connect_error); // Pesan detail hanya di lingkungan non-produksi
    }
}
?>
