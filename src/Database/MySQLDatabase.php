<?php

namespace Nebula\Database;

use Nebula\Interfaces\Database\Database;
use Nebula\Traits\Instance\Singleton;
use PDO;
use PDOStatement;

class MySQLDatabase implements Database
{
    use Singleton;

    private PDO $connection;
    private array $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    public function connect(array $config): void
    {
        extract($config);
        $dsn = "mysql:host={$host};port={$port};dbname={$name}";
        $this->connection = new PDO($dsn, $username, $password, $this->options);
    }

    public function query(string $sql, ...$params): ?PDOStatement
    {
        $statement = $this->connection->prepare($sql);
        $result = $statement->execute($params);
        return $result ? $statement : null;
    }

    public function __call($method, $args)
    {
        return $this->connection->$method(...$args);
    }
}
