<?php

declare(strict_types=1);

namespace App\Core\Database;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private const LOG_PATH = '/var/log/sql.log';

    public static ?PDO $db = null;

    public static function dbConnect(): PDO
    {
        if (self::$db === null) {
            try {
                self::$db = new PDO(
                    'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'],
                    $_ENV['DB_USERNAME'],
                    $_ENV['DB_PASSWORD'],
                    [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
                );

                self::$db->exec('SET NAMES UTF8');
            } catch (PDOException $e) {
                self::handleException($e, 'Database connection failed.');
            }
        }

        return self::$db;
    }

    public function doQuery(string $sql, array $params = []): ?PDOStatement
    {
        try {
            self::dbConnect();

            $query = self::$db->prepare($sql);

            foreach ($params as $key => $value) {
                $query->bindValue($key, $value);
            }

            $query->execute();

            return $query;
        } catch (PDOException $e) {
            self::handleException($e, 'Error executing query');

            return null;
        }
    }

    public function insertAndGetId(string $sql, array $params = []): int
    {
        try {
            $this->doQuery($sql, $params);

            return (int)self::$db->lastInsertId();
        } catch (PDOException $e) {
            self::handleException($e, 'Error inserting and getting ID');

            return 0;
        }
    }

    protected static function handleException(PDOException $e, string $context = ''): void
    {
        $message = date('Y-m-d H:i:s') . " - {$context}: {$e->getMessage()}";

        file_put_contents(self::LOG_PATH, $message, FILE_APPEND);
    }

    public function first(?PDOStatement $query): ?array
    {
        $data = $query->fetchAll();

        return $data[0] ?? null;
    }
}
