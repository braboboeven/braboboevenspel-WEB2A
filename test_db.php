<?php
require_once "vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

$host = $_ENV['Host'];
$username = $_ENV['Username'];
$password = $_ENV['Password'];
$db = $_ENV['Db'];

echo "Probeer te verbinden met:<br>";
echo "Host: " . $host . "<br>";
echo "User: " . $username . "<br>";
echo "Database: " . $db . "<br><br>";

$conn = new mysqli($host, $username, $password, $db);

if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
} else {
    echo "✓ Verbinding succesvol!";
}
?>