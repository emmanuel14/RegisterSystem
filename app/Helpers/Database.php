<?php

namespace Helpers;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Database – Singleton PDO wrapper with prepared statement helpers.
 */
class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST, DB_NAME, DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
            die(json_encode(['error' => 'Database connection failed. Please try again later.']));
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** Execute a query and return the statement. */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Fetch a single row. */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row ?: null;
    }

    /** Fetch all rows. */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /** Fetch a single column value. */
    public function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    /** Insert a row and return the last insert ID. */
    public function insert(string $table, array $data): string
    {
        $cols    = implode(', ', array_map(fn($k) => "`$k`", array_keys($data)));
        $holders = implode(', ', array_fill(0, count($data), '?'));
        $this->query("INSERT INTO `{$table}` ({$cols}) VALUES ({$holders})", array_values($data));
        return $this->pdo->lastInsertId();
    }

    /** Update rows and return affected count. */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(', ', array_map(fn($k) => "`$k` = ?", array_keys($data)));
        $stmt = $this->query(
            "UPDATE `{$table}` SET {$set} WHERE {$where}",
            [...array_values($data), ...$whereParams]
        );
        return $stmt->rowCount();
    }

    /** Delete rows. */
    public function delete(string $table, string $where, array $params = []): int
    {
        return $this->query("DELETE FROM `{$table}` WHERE {$where}", $params)->rowCount();
    }

    /** Count rows. */
    public function count(string $table, string $where = '1', array $params = []): int
    {
        return (int) $this->fetchColumn("SELECT COUNT(*) FROM `{$table}` WHERE {$where}", $params);
    }

    /** Transactions. */
    public function beginTransaction(): void  { $this->pdo->beginTransaction(); }
    public function commit(): void            { $this->pdo->commit(); }
    public function rollback(): void          { $this->pdo->rollBack(); }

    /** Run a transaction closure safely. */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /** Get raw PDO (escape hatch for complex queries). */
    public function getPdo(): PDO { return $this->pdo; }

    private function logError(string $message): void
    {
        $log = STORAGE_PATH . '/logs/db_errors.log';
        @file_put_contents($log, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
    }

    private function __clone() {}
}
