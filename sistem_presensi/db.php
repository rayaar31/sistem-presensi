<?php
$db = parse_url(getenv("DATABASE_URL"));
$host = $db["host"];
$port = $db["port"];
$user = $db["user"];
$pass = $db["pass"];
$dbname = ltrim($db["path"], '/');

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
