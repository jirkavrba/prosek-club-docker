<?php

namespace ProsekClub\Demo;

class Database
{
    private static ?\PDO $connection = null;

    private function __construct() {}

    public static function getConnection(): \PDO
    {
        if (self::$connection === null) {
            $host = getenv("DB_HOST") ?: "127.0.0.1";
            $dbname = getenv("DB_NAME") ?: "todos";
            $user = getenv("DB_USER") ?: "todos";
            $password = getenv("DB_PASS") ?: "t0d0s";

            self::$connection = new \PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $user,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ],
            );
        }

        return self::$connection;
    }
}
