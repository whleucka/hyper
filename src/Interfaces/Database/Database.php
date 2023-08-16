<?php
namespace Nebula\Interfaces\Database;

use PDOStatement;

interface Database
{
    public function connect(array $config): void;
    public function run(string $sql, array $params = []): ?PDOStatement;
}
