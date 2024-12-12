<?php
// Mengambil DATABASE_URL dari environment variables Railway
$databaseUrl = getenv('DATABASE_URL');

// Parse DATABASE_URL untuk mendapatkan komponen-komponennya
$parsedUrl = parse_url($databaseUrl);

// Menyusun informasi koneksi dari hasil parsing
$host = $parsedUrl['host'];        // Host database (biasanya berupa hostname atau IP address)
$username = $parsedUrl['user'];    // Username untuk database
$password = $parsedUrl['pass'];    // Password untuk database
$dbname = ltrim($parsedUrl['path'], '/'); // Nama database (diambil dari path)

// Koneksi ke database MySQL menggunakan mysqli
$conn = new mysqli($host, $username, $password, $dbname);

// Mengecek apakah koneksi berhasil
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
