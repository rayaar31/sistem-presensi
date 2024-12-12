$dbUrl = getenv('DATABASE_URL');
if (!$dbUrl) {
    die("Error: DATABASE_URL tidak ditemukan. Tambahkan pada Environment Railway.");
}
$parsedUrl = parse_url($dbUrl);

$db_host = $parsedUrl['host'];
$db_user = $parsedUrl['user'];
$db_pass = $parsedUrl['pass'];
$db_name = ltrim($parsedUrl['path'], '/');

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
