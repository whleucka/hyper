<?php

namespace Nebula\Models;

use Nebula\Container\Container;
use GalaxyPDO\DB;

class Model
{
    protected DB $db;
    protected Container $container;
    private string $table_name;
    private string $primary_key;
    private ?string $id;
    private bool $exists = false;
    private array $attributes = [];
    private array $public_properties = [];
    private array $private_properties = [];

    public function __construct(
        string $table_name,
        string $primary_key,
        ?string $id = null
    ) {
        $this->db = app()->getDatabase();
        $this->table_name = $table_name;
        $this->primary_key = $primary_key;
        $this->id = $id;
        $this->loadAttributes();
    }

    /**
     * Find a model in the database
     */
    public static function find(mixed $id): ?Model
    {
        $class = static::class;
        $model = new $class($id);
        if ($model->exists()) {
            return $model;
        }
        return null;
    }

    /**
     * Find a model in the database by attribute
     */
    public static function findByAttribute(
        string $attribute,
        mixed $value
    ): ?Model {
        $class = static::class;
        $model = new $class();
        $id = $model->db->selectVar(
            "SELECT $model->primary_key 
            FROM $model->table_name 
            WHERE $attribute = ?",
            $value
        );
        if ($id) {
            return new $class($id);
        }
        return null;
    }

    /**
     * Load attributes and private/public properties
     */
    private function loadAttributes(): void
    {
        $class = static::class;
        $reflection = new \ReflectionClass($class);
        $private_properties = $reflection->getProperties(
            \ReflectionProperty::IS_PRIVATE
        );
        $public_properties = $reflection->getProperties(
            \ReflectionProperty::IS_PUBLIC
        );
        if (!is_null($this->id)) {
            $row = $this->db->selectOne(
                "SELECT * FROM $this->table_name WHERE $this->primary_key = ?",
                $this->id
            );
            if ($row) {
                $this->exists = true;
            }
        }
        foreach ([...$private_properties, ...$public_properties] as $one) {
            $property = $one->name;
            $this->attributes[$property] = $row?->$property ?? null;
        }
        $this->public_properties = array_map(
            fn($public) => $public->name,
            $public_properties
        );
        $this->private_properties = array_map(
            fn($private) => $private->name,
            $private_properties
        );
        if ($this->exists()) {
            $this->fillProperties();
        }
    }

    /**
     * Fills public / private properties
     */
    public function fillProperties(): void
    {
        foreach (
            [$this->public_properties, $this->private_properties]
            as $properties
        ) {
            foreach ($properties as $column) {
                if (property_exists($this, $column) && !isset($this->$column)) {
                    if (isset($this->attributes[$column])) {
                        $this->$column = $this->attributes[$column];
                    }
                }
            }
        }
    }

    /**
     * Formatted columns for insert query
     */
    public function getFormattedColumns(): string
    {
        // Keys are the columns names
        $columns = $this->public_properties;
        $stmt = array_map(fn($column) => $column . " = ?", $columns);
        return implode(", ", $stmt);
    }

    /**
     * Insert model to database
     */
    public function insert(): ?Model
    {
        $columns = $this->getFormattedColumns();
        $values = array_map(
            fn($public) => $this->$public ?? null,
            $this->public_properties
        );
        $result = $this->db->query(
            "INSERT INTO $this->table_name SET $columns",
            ...array_values($values)
        );
        if ($result) {
            $id = $this->db->lastInsertId();
            $class = static::class;
            return new $class($id);
        }
        return null;
    }

    /**
     * Update model in database
     */
    public function update(): bool
    {
        $columns = $this->getFormattedColumns();
        $values = array_map(
            fn($public) => $this->$public ?? null,
            $this->public_properties
        );
        // Add the id to the values array as the last entry
        $values[] = $this->id;
        $result = $this->db->query(
            "UPDATE $this->table_name SET $columns WHERE $this->primary_key = ?",
            ...array_values($values)
        );
        if ($result) {
            $this->loadAttributes();
            return true;
        }
        return false;
    }

    /**
     * Delete model in database
     */
    public function delete(): bool
    {
        $result = $this->db->query(
            "DELETE FROM $this->table_name WHERE $this->primary_key = ?",
            $this->id
        );
        if ($result) {
            $this->loadAttributes();
            return true;
        }
        return false;
    }

    /**
     * Does this model exist in the database?
     */
    public function exists(): bool
    {
        return $this->exists === true;
    }

    /**
     * @param mixed $name
     */
    public function __get($name): mixed
    {
        return $this->attributes[$name];
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value): void
    {
        // Only allow setting pulbic properties
        if (in_array($name, $this->public_properties)) {
            $this->attributes[$name] = $value;
        }
    }
}
