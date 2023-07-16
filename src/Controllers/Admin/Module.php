<?php

namespace Nebula\Controllers\Admin;

use Error;
use Exception;
use PDO;

class Module
{
    private $model_id;
    protected array $table = [];
    protected array $form = [];
    protected array $create_validation = [];
    protected array $modify_validation = [];

    /**
     * @param array<int,mixed> $config
     */
    public function __construct(
        protected array $config,
        ?string $model_id = null
    ) {
        $this->model_id = $model_id;
    }

    public function __get(string $name): mixed
    {
        return $this->config[$name] ?? null;
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value): void
    {
        $this->config[$name] = $value;
    }

    public function getRoute($name): string
    {
        return $this->route . ".$name";
    }

    public function validation(string $type): array
    {
        return match ($type) {
            "create" => $this->create_validation,
            "modify" => $this->modify_validation,
            default => throw new Error("unknown validation type"),
        };
    }

    protected function getPrimaryKey(): string
    {
        return $this->primary_key ?? "id";
    }

    protected function commaSep(array $columns): string
    {
        return implode(", ", array_values($columns));
    }

    protected function setColumns(array $columns): string
    {
        $stmt = array_map(fn($column) => $column . " = ?", $columns);
        return implode(", ", $stmt);
    }

    public function tableData(): array
    {
        if (empty($this->table) || !isset($this->config["table"])) {
            return [];
        }
        $table_name = $this->config["table"];
        $columns = $this->commaSep($this->table);
        try {
            $result = db()
                ->run("SELECT $columns FROM $table_name")
                ->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            throw new Error("table data query");
        }
        return $result;
    }

    public function formData(): array
    {
        if (empty($this->table) || !isset($this->config["table"])) {
            return [];
        }
        $table_name = $this->config["table"];
        $columns = $this->commaSep($this->form);
        $primary_key = $this->getPrimaryKey();
        try {
            $result = db()
                ->run(
                    "SELECT $columns FROM $table_name WHERE $primary_key = ?",
                    [$this->model_id]
                )
                ->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            throw new Error("form data query");
        }
        // Return an empty form if there is no result
        return $result ? $result : [];
    }

    /**
     * Get data for the twig template
     * @return array<string,string>
     */
    public function getData(): array
    {
        return $this->mergeData();
    }

    public function formRequestValues(): array
    {
        return array_values(
            array_map(
                fn($column) => request()->get($column) ?? null,
                $this->form
            )
        );
    }

    public function update(): bool
    {
        $values = [...$this->formRequestValues(), $this->model_id];
        $columns = $this->setColumns($this->form);
        $table_name = $this->config["table"];
        $primary_key = $this->getPrimaryKey();
        $result = db()->query(
            "UPDATE $table_name SET $columns WHERE $primary_key = ?",
            ...$values
        );
        return $result ? true : false;
    }

    public function insert(): string|false
    {
        $values = $this->formRequestValues();
        $columns = $this->setColumns($this->form);
        $table_name = $this->config["table"];
        $result = db()->query(
            "INSERT INTO $table_name SET $columns",
            ...$values
        );
        if ($result) {
            return db()->lastInsertId();
        }
        return false;
    }

    public function delete(): bool
    {
        $table_name = $this->config["table"];
        $primary_key = $this->getPrimaryKey();
        $result = db()->query(
            "DELETE FROM $table_name WHERE $primary_key = ?",
            $this->model_id
        );
        return $result ? true : false;
    }

    protected function table(): array
    {
        return [
            "content" => twig("layouts/table.html", [
                "route" => $this->route,
                "primary_key" => $this->getPrimaryKey(),
                "edit_route" => $this->getRoute("edit"),
                "destroy_route" => $this->getRoute("destroy"),
                "columns" => array_keys($this->table),
                "data" => $this->tableData(),
                "edit_enabled" => $this->edit_enabled ?? false,
                "destroy_enabled" => $this->destroy_enabled ?? false,
            ]),
        ];
    }

    protected function form(): array
    {
        return [
            "content" => twig("layouts/form.html", [
                "route" => $this->route,
                "primary_key" => $this->getPrimaryKey(),
                "index_route" => $this->getRoute("index"),
                "edit_route" => $this->getRoute("edit"),
                "modify_route" => $this->getRoute("modify"),
                "destroy_route" => $this->getRoute("destroy"),
                "form" => $this->form,
                "data" => $this->formData(),
                "edit_enabled" => $this->edit_enabled ?? false,
                "destroy_enabled" => $this->destroy_enabled ?? false,
                "model_id" => $this->model_id,
            ]),
        ];
    }
    /**
     * Override function for twig data
     */
    protected function data(): array
    {
        $route = app()->getRoute();
        // These are for views only
        return match ($route->getName()) {
            "module.index" => $this->table(),
            "module.edit", "module.create", "module.store", "module.modify",
            "module.destroy" => $this->form(),
            default => throw new Error(
                "module data error: route name not defined '{$route->getName()}'"
            ),
        };
    }

    /**
     * Merge default twig data and data()
     * @return array<string,string>
     */
    private function mergeData(): array
    {
        $default = [
            "route" => $this->route,
            "link" => app()->moduleRoute($this->getRoute("index")),
            "parent" => $this->parent,
            "title" => $this->title,
            "modules" => $this->getModules(),
            "icon" => $this->icon ?? "box",
            "add_enabled" => $this->add_enabled ?? false,
            "content" => "",
        ];
        return array_replace($default, $this->data());
    }

    /**
     * Modules for sidebar display
     * @return array<int,array>
     */
    protected function getModules(): array
    {
        $config = config("paths")["modules"];
        $map = app()->classMap($config);
        $modules = [];
        foreach ($map as $class => $file) {
            $class = new $class();
            $modules[$class?->parent ?? "Adminstration"][] = [
                "link" => $class->getRoute("index"),
                "title" => $class->title,
                "parent" => $class->parent,
                "icon" => $class->icon ?? "box",
            ];
        }
        return $modules;
    }
}
