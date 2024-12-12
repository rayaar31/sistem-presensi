<?php
require_once __DIR__ . '/vendor/autoload.php'; // Memuat autoloader Composer

// Memuat file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Membaca konfigurasi dari .env
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db = getenv('DB_NAME');

date_default_timezone_set("Asia/Jakarta");

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
