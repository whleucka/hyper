<?php

namespace Nebula\Models;

use Nebula\Container\Container;
use GalaxyPDO\DB;
use PDO;

class Model
{
    protected DB $db;
    protected Container $container;
    private string $table_name;
    private string $primary_key;
    private ?string $id;
    private bool $exists = false;
    private array $attributes = [];
    private array $properties = [];
    protected array $guarded = [];

    public function __construct(
        string $table_name,
        string $primary_key,
        ?string $id = null
    ) {
        $this->table_name = $table_name;
        $this->primary_key = $primary_key;
        $this->id = $id;
        $this->loadProperties();
        $this->loadAttributes();
    }

    /**
     * Return the $this->primary_key value
     */
    public function getId(): ?string
    {
        return $this->{$this->primary_key};
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
        $id = db()->selectVar(
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

    private function loadProperties(): void
    {
        $class = static::class;
        $reflection = new \ReflectionClass($class);
        $this->properties = array_filter(
            $reflection->getProperties(),
            fn($ref) => $ref->class === $class
        );
        $this->properties = array_map(
            fn($property) => $property->name,
            $this->properties
        );
    }

    /**
     * Load attributes and private/public properties
     */
    private function loadAttributes(): void
    {
        if (!is_null($this->id)) {
            $this->loadFromId();
        } else {
            $this->loadFromSchema();
        }
        if ($this->exists()) {
            $this->fillProperties();
        }
    }

    /**
     * Return the model from the database by ID
     */
    private function loadFromId(): void
    {
        $model = db()->selectOne(
            "SELECT * FROM $this->table_name WHERE $this->primary_key = ?",
            $this->id
        );
        if ($model) {
            $this->exists = true;
        }
        foreach ($this->properties as $property) {
            if ($model && property_exists($model, $property)) {
                $this->attributes[$property] = $model->$property;
            }
        }
    }

    /**
     * Return the model from the database by table schema
     */
    private function loadFromSchema(): void
    {
        $desc = db()->query("DESCRIBE $this->table_name");
        $columns = $desc->fetchAll(PDO::FETCH_COLUMN);
        foreach ($columns as $name) {
            $this->attributes[$name] = null;
        }
    }

    /**
     * Fills all properties
     */
    public function fillProperties(): void
    {
        foreach ($this->properties as $property) {
            if (property_exists($this, $property) && !isset($this->$property)) {
                if (isset($this->attributes[$property])) {
                    $this->$property = $this->attributes[$property];
                }
            }
        }
    }

    /**
     * Formatted columns for query
     */
    public function getFormattedColumns(): string
    {
        $columns = array_filter(
            $this->properties,
            fn($property) => key_exists($property, $this->attributes) &&
                !in_array($property, $this->guarded)
        );
        $stmt = array_map(fn($column) => $column . " = ?", $columns);
        return implode(", ", $stmt);
    }

    /**
     * We only want public properties that exists as an entity attribute
     */
    public function attributeValues(): array
    {
        $columns = array_filter(
            $this->properties,
            fn($property) => key_exists($property, $this->attributes) &&
                !in_array($property, $this->guarded)
        );
        return array_map(fn($property) => $this->$property ?? null, $columns);
    }

    /**
     * Insert model to database
     */
    public function insert(): ?Model
    {
        $columns = $this->getFormattedColumns();
        $values = $this->attributeValues();
        $result = db()->query(
            "INSERT INTO $this->table_name SET $columns",
            ...array_values($values)
        );
        if ($result) {
            $id = db()->lastInsertId();
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
        $values = $this->attributeValues();
        // Add the id to the values array as the last entry
        $values[] = $this->id;
        $result = db()->query(
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
        $result = db()->query(
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
        $this->attributes[$name] = $value;
    }
}
