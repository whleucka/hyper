<?php

namespace Nebula\Database;

use Nebula\Interfaces\Database\Database;
use Nebula\Traits\Instance\Singleton;
use PDO;
use PDOStatement;
use stdClass;

class MySQLDatabase implements Database
{
    use Singleton;

    private PDO $connection;
    private array $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    public $fetch_type = PDO::FETCH_OBJ;

    public function connect(array $config): void
    {
        extract($config);
        $dsn = "mysql:host={$host};port={$port};dbname={$name}";
        $this->connection = new PDO($dsn, $username, $password, $this->options);
    }

    public function isConnected()
    {
        return isset($this->connection);
    }

    public function run(string $sql, array $params = []): ?PDOStatement
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    public function selectAll(string $sql, ...$params): ?array
    {
        $result = $this->run($sql, $params); 
        return $result?->fetchAll($this->fetch_type);
    }

    public function select(string $sql, ...$params): mixed
    {
        $result = $this->run($sql, $params); 
        return $result?->fetch($this->fetch_type);
    }

    public function query(string $sql, ...$params): ?PDOStatement
    {
        $statement = $this->run($sql, $params); 
        return $statement;
    }

    public function __call($method, $args)
    {
        return $this->connection->$method(...$args);
    }
}
