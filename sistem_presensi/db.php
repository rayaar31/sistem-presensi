<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'sistem_presensi';
date_default_timezone_set("Asia/Jakarta");


$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

