<?php
declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private static string $host = "127.0.0.1";
    private static int $port = 3306;
    private static string $dbname = "pre";
    private static string $username = "root";
    private static string $password = "";

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$dbname . ";charset=utf8mb4";
                self::$instance = new PDO($dsn, self::$username, self::$password, $options);
            } catch (PDOException $e) {
                // Fallback secundario si la base de datos pre no existiera
                try {
                    $dsnFallback = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=cultura_peru;charset=utf8mb4";
                    self::$instance = new PDO($dsnFallback, self::$username, self::$password, $options);
                } catch (PDOException $fallbackError) {
                    error_log("Database connection failed: " . $e->getMessage());
                    die("Error crítico: No se pudo conectar a la base de datos.");
                }
            }
        }

        return self::$instance;
    }

    private function __construct()
    {
    }
}

