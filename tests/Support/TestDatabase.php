<?php

declare(strict_types=1);

namespace Gallerio\Tests\Support;

use PDO;

/**
 * Connection helpers for the dedicated MariaDB test database.
 */
final class TestDatabase
{
    public static function connect(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=gallerio_test;charset=utf8mb4',
            \DB_HOST,
            \DB_PORT
        );

        return new PDO($dsn, \DB_USER, \DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public static function reset(PDO $pdo): void
    {
        $pdo->exec('DELETE FROM images');
    }
}
