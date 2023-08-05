<?php

namespace Nebula\Database;

use Closure;
use Nebula\Interfaces\Database\QueryBuilder as QueryBuilderInterface;
use Nebula\Interfaces\Model\Model;

class QueryBuilder implements QueryBuilderInterface
{
    private $mode = "select";
    private string $table_name = "";
    private array $values = [];
    private array $columns = [];
    private array $where = [];
    private array $having = [];
    private array $group_by = [];
    private array $order_by = [];
    private ?int $limit = null;
    private ?int $offset = null;

    public function values(): array
    {
        return $this->values;
    }

    /**
     * @param array<int,mixed> $data
     * @param Closure(): void $fn
     * @param string $separator
     */
    private function mapToString(array $data, \Closure $fn, string $separator = ", "): string
    {
        $columns = array_map($fn, $data);
        return implode($separator, $columns);
    }

    public static function select(Model $model): self
    {
        $qb = new QueryBuilder();
        $qb->table_name = $model->table_name;
        $qb->mode = "select";
        return $qb;
    }

    public static function insert(Model $model): self
    {
        $qb = new QueryBuilder();
        $qb->table_name = $model->table_name;
        $qb->mode = "insert";
        return $qb;
    }

    public static function update(Model $model): self
    {
        $qb = new QueryBuilder();
        $qb->table_name = $model->table_name;
        $qb->mode = "update";
        return $qb;
    }

    public static function delete(Model $model): self
    {
        $qb = new QueryBuilder();
        $qb->table_name = $model->table_name;
        $qb->mode = "delete";
        return $qb;
    }

    /**
     * @param array<int,mixed> $columns
     */
    public function columns(array $columns): self
    {
        if ($this->mode === "insert" || $this->mode === "update") {
            $this->values = array_merge($this->values, array_values($columns));
        }
        $this->columns = $columns;
        return $this;
    }

    public function where(array $where): self
    {
        $this->where = $where;
        $this->values = array_merge($this->values, array_values($where));
        return $this;
    }

    public function groupBy(array $group_by): self
    {
        $this->group_by = $group_by;
        return $this;
    }

    public function having(array $having): self
    {
        $this->having = $having;
        $this->values = array_merge($this->values, array_values($having));
        return $this;
    }


    public function orderBy(array $order_by): self
    {
        $this->order_by = $order_by;
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function build(): string
    {
        $sql = match ($this->mode) {
            "select" => "SELECT * FROM $this->table_name ",
            "insert" => "INSERT INTO $this->table_name SET ",
            "update" => "UPDATE $this->table_name SET ",
            "delete" => "DELETE FROM $this->table_name ",
        };
        if ($this->mode === "select") {
            if (!empty($this->columns)) {
                $select_stmt = implode(', ', $this->columns);
                $sql = str_replace('*', $select_stmt, $sql);
            }
            if (!empty($this->where)) {
                $where_clause = $this->mapToString(array_keys($this->where), fn ($key) => "($key = ?)", " AND ");
                $sql .= "WHERE $where_clause ";
            }
            if (!empty($this->group_by)) {
                $group_by_clause = $this->mapToString($this->group_by, fn ($column) => $column, ", ");
                $sql .= "GROUP BY $group_by_clause ";
            }
            if (!empty($this->having)) {
                $having_clause = $this->mapToString(array_values($this->group_by), fn ($key) => "($key = ?)", " AND ");
                $sql .= "HAVING $having_clause ";
            }
            if (!empty($this->order_by)) {
                $order_by_clause = implode(", ", array_map(fn ($key, $value) => "$key $value", array_keys($this->order_by), array_values($this->order_by)));
                $sql .= "ORDER BY $order_by_clause ";
            }
            if (!is_null($this->limit)) {
                $sql .= "LIMIT $this->limit";
            }
            if (!is_null($this->offset)) {
                $sql .= ", $this->offset";
            }
        } else {
            if (!empty($this->columns)) {
                $columns = $this->mapToString(array_keys($this->columns), fn ($key) => "$key = ?", ", ");
                $sql .= "$columns ";
            }
        }
        if ($this->mode === "update" || $this->mode === "delete") {
            if (!empty($this->where)) {
                $where_clause = $this->mapToString(array_keys($this->where), fn ($key) => "($key = ?)", " AND ");
                $sql .= "WHERE $where_clause ";
            }
        }
        return trim($sql);
    }
}
