<?php

namespace Nebula\Database;

use Nebula\Interfaces\Database\Database;
use Nebula\Traits\Instance\Singleton;
use PDO;
use PDOStatement;
use stdClass;

/**
 * MySQL Database
 */
class MySQLDatabase implements Database
{
    use Singleton;

    /**
     * Database connection
     * @var PDO
     */
    private PDO $connection;
    /**
     * PDO options
     * @var array
     */
    private array $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    /**
     * Fetch type
     * @var int
     */
    public $fetch_type = PDO::FETCH_OBJ;

    /**
     * Connect to the database
     * @param array $config Database configuration
     */
    public function connect(array $config): void
    {
        // Extract the database configuration
        extract($config);
        $dsn = "mysql:host={$host};port={$port};dbname={$name}";
        $this->connection = new PDO($dsn, $username, $password, $this->options);
    }

    /**
     * Check if the database is connected
     * @return bool
     */
    public function isConnected(): bool
    {
        return isset($this->connection);
    }

    /**
     * Run a query
     * @param string $sql SQL query
     * @param array $params SQL query parameters
     * @return PDOStatement|null
     */
    public function run(string $sql, array $params = []): ?PDOStatement
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    /**
     * Select all rows
     * @param string $sql SQL query
     * @param array $params SQL query parameters
     * @return array|null
     */
    public function selectAll(string $sql, ...$params): ?array
    {
        $result = $this->run($sql, $params);
        return $result?->fetchAll($this->fetch_type);
    }

    /**
     * Select a single row
     * @param string $sql SQL query
     * @param array $params SQL query parameters
     * @return stdClass|null
     */
    public function select(string $sql, ...$params): mixed
    {
        $result = $this->run($sql, $params);
        return $result?->fetch($this->fetch_type);
    }

    /**
     * Run a query
     * @param string $sql SQL query
     * @pearam array $params SQL query parameters
     */
    public function query(string $sql, ...$params): ?PDOStatement
    {
        $statement = $this->run($sql, $params);
        return $statement;
    }

    /**
     * Call PDO methods
     * @param string $method PDO method
     * @param array $args PDO method arguments
     */
    public function __call($method, $args)
    {
        return $this->connection->$method(...$args);
    }
}
