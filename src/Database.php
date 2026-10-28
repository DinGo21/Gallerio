<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/*
 * Shared PDO connection to the Gallerio MariaDB database.
 *
 * Usage: Database::connection()->prepare(...)
 */
final class Database
{
    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    /**
     * Return the shared PDO connection, creating it on first use.
     *
     * @throws PDOException when the database is unreachable.
     */
    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                DB_HOST,
                DB_PORT,
                DB_NAME
            );

            self::$connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$connection;
    }
}
