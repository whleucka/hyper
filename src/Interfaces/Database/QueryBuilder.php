<?php

namespace Nebula\Interfaces\Database;

use Nebula\Interfaces\Model\Model;

interface QueryBuilder
{
  public function build(): string;
  public static function select(Model $model): self;
  public static function insert(Model $model): self;
  public static function update(Model $model): self;
  public static function delete(Model $model): self;
  public function where(array $columns): self;
  public function having(array $columns): self;
  public function groupBy(array $columns): self;
  public function orderBy(array $columns): self;
  public function limit(int $limit): self;
  public function offset(int $offset): self;
}
