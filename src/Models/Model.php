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
    private bool $loaded = false;
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
                $this->loaded = true;
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
    }

    /**
     * Formatted columns for insert query
     */
    public function getInsertColumns(): string
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
        $columns = $this->getInsertColumns();
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

    public function isLoaded(): bool
    {
        return $this->loaded === true;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
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
