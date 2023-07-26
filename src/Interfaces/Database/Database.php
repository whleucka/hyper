<?php

namespace Nebula\Interfaces\Database;

interface Database
{
  public function connect(array $config): void;
  public function query(string $sql, ...$params): bool;
}
