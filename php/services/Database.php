<?php

class Database
{
    public static function getConnection(): mysqli
    {
        // Expected env variables: Host, Username, Password, Db
        $envPath = __DIR__ . '/../../.env';
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || substr($line, 0, 1) === '#') {
                    continue;
                }
                [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
                $_ENV[$key] = $value;
                putenv($line);
            }
        }

        $host = $_ENV['Host'] ?? '127.0.0.1';
        $username = $_ENV['Username'] ?? 'root';
        $password = $_ENV['Password'] ?? '';
        $db = $_ENV['Db'] ?? '';

        $conn = new mysqli($host, $username, $password, $db);
        if ($conn->connect_error) {
            throw new RuntimeException('Database connect error: ' . $conn->connect_error);
        }

        return $conn;
    }
}

?>
