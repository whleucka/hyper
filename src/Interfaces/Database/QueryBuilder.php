<?php

namespace Nebula\Interfaces\Database;

interface QueryBuilder
{
    public function build(): string;
    public function values(): array;
    public static function select(string $table_name): self;
    public static function insert(string $table_name): self;
    public static function update(string $table_name): self;
    public static function delete(string $table_name): self;
    public function columns(array $columns): self;
    public function where(...$args): self;
    public function having(...$args): self;
    public function groupBy(array $columns): self;
    public function orderBy(array $columns): self;
    public function limit(?int $limit): self;
    public function offset(?int $offset): self;
}
