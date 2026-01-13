<?php
// db_connect.php - Database connectie voor timer systeem
require_once "./vendor/autoload.php";

// Laad .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

$host = $_ENV['Host'];
$username = $_ENV['Username'];
$password = $_ENV['Password'];
$db = $_ENV['Db'];

// Maak verbinding met MySQL
$conn = new mysqli($host, $username, $password, $db);

// Foutafhandeling bij connectie
if ($conn->connect_error) {
    die("Database connectie mislukt: " . $conn->connect_error);
}

// Stel character set in
$conn->set_charset("utf8mb4");
?>