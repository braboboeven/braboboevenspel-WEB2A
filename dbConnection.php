<?php
//Deze file en dbCredentials.env moeten als root worden gebruikt met require_once.
//Gebruik $connectDB = DB::getConn(); om te connecten met de database in code.
class DB
{
    private static ?mysqli $conn = null;

    public static function getConn(): ?mysqli
    {
        if (self::$conn === null) {
            $envFile = __DIR__ . '/../.env';

            if (!file_exists($envFile)) {
                error_log("DB: .env file not found");
                return null;
            }

            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) continue;
                [$key, $value] = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }

            self::$conn = new mysqli(
                $_ENV['DB_HOST'],
                $_ENV['DB_USER'],
                $_ENV['DB_PASS'],
                $_ENV['DB_NAME']
            );

            if (self::$conn->connect_error) {
                error_log("DB: connection failed: " . self::$conn->connect_error);
                self::$conn = null;
                return null;
            }
        }

        return self::$conn;
    }
}
?>
