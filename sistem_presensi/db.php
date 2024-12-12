<?php
session_start();

$databaseUrl = getenv('DATABASE_URL');
if (!$databaseUrl) {
    die("Error: DATABASE_URL tidak ditemukan.");
}

$parsedUrl = parse_url($databaseUrl);

// Ambil detail koneksi dari DATABASE_URL
$db_host = $parsedUrl["host"];
$db_user = $parsedUrl["user"];
$db_pass = $parsedUrl["pass"];
$db_name = ltrim($parsedUrl["path"], '/');

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
