<?php

namespace Nebula\Database;

use Nebula\Interfaces\Database\QueryBuilder as QueryBuilderInterface;
use Nebula\Interfaces\Model\Model;

class QueryBuilder implements QueryBuilderInterface
{
    private string $sql = "";
    private array $values = [];

    public function values(): array
    {
        return $this->values;
    }

    public static function select(Model $model): self
    {
        $qb = new QueryBuilder();
        $qb->sql = "SELECT * FROM $model->table_name ";
        return $qb;
    }

    public static function insert(Model $model): self
    {
        $qb = new QueryBuilder();
        $qb->sql = "INSERT INTO $model->table_name SET ";
        return $qb;
    }

    public static function update(Model $model): self
    {
        $qb = new QueryBuilder();
        $qb->sql = "UPDATE $model->table_name SET ";
        return $qb;
    }

    public static function delete(Model $model): self
    {
        $qb = new QueryBuilder();
        $qb->sql = "DELETE FROM $model->table_name ";
        return $qb;
    }

    public function columns(array $columns): self
    {
        $columns = implode(', ', $columns);
        $this->sql = str_replace('*', $columns, $this->sql);
        return $this;
    }

    private function mapToString(array $data, \Closure $fn, $separator = ", "): string
    {
        $columns = array_map($fn, $data);
        return implode($separator, $columns);
    }

    public function where(array $columns): self
    {
        $having_clause = $this->mapToString(array_keys($columns), fn ($key) => "($key = ?)", " AND ");
        $this->sql .= "WHERE $having_clause ";
        $this->values = array_merge($this->values, array_values($columns));
        return $this;
    }

    public function having(array $columns): self
    {
        $having_clause = $this->mapToString(array_keys($columns), fn ($key) => "($key = ?)", " AND ");
        $this->sql .= "HAVING $having_clause ";
        $this->values = array_merge($this->values, array_values($columns));
        return $this;
    }

    public function groupBy(array $columns): self
    {
        $group_by_clause = $this->mapToString($columns, fn ($column) => $column, ", ");
        $this->sql .= "GROUP BY $group_by_clause ";
        return $this;
    }

    public function orderBy(array $columns): self
    {
        $order_by_clause = implode(", ", array_map(fn ($key, $value) => "$key $value", array_keys($columns), array_values($columns)));
        $this->sql .= "ORDER BY $order_by_clause ";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->sql .= "LIMIT $limit";
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->sql .= ", $offset";
        return $this;
    }

    public function build(): string
    {
        return $this->sql;
    }
}
