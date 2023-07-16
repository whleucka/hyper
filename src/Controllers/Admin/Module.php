<?php

namespace Nebula\Controllers\Admin;

use Error;
use Exception;
use PDO;

class Module
{
    /** Queries */
    private ?string $model_id;
    public string $primary_key = "id";
    public string $table_name = "";
    public array $table = [];
    public array $form = [];
    /** Validation */
    public array $create_validation = [];
    public array $modify_validation = [];
    /** Permissions */
    public $create_enabled;
    public $edit_enabled;
    public $destroy_enabled;
    /** Routes */
    public $route;
    public $index_route;
    public $edit_route;
    public $modify_route;
    public $create_route;
    public $destroy_route;
    /** Meta */
    public $title;
    public $parent;
    public $icon;

    public function __construct(?string $model_id = null)
    {
        $this->model_id = $model_id;
    }

    /**
     * Build a route name by type
     */
    public function routeName(string $type): string
    {
        return $this->route . ".$type";
    }

    /**
     * Return validation array by route type
     */
    public function validationArray(string $route_type): array
    {
        return match ($route_type) {
            "create" => $this->create_validation,
            "modify" => $this->modify_validation,
            default => throw new Error("unknown validation type"),
        };
    }

    /**
     * Return comma separated string of columns
     * @param array<int,mixed> $columns
     */
    protected function commaColumns(array $columns): string
    {
        return implode(", ", array_values($columns));
    }

    /**
     * Return placeholder string "column1 = ?", "column2 = ?"
     * @param array<int,mixed> $columns
     */
    protected function placeholderColumns(array $columns): string
    {
        $stmt = array_map(fn($column) => $column . " = ?", $columns);
        return $this->commaColumns($stmt);
    }

    /**
     * Return array of form request vlaues
     */
    protected function formRequestValues(): array
    {
        return array_values(
            array_map(
                fn($column) => request()->get($column) ?? null,
                $this->form
            )
        );
    }

    /**
     * Table data query
     */
    public function tableData(): array
    {
        $result = [];
        try {
            $columns = $this->commaColumns($this->table);
            $result = db()
                ->run("SELECT $columns FROM $this->table_name")
                ->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            if (!app()->isDebug()) {
                app()->serverError();
            }
        }
        return $result;
    }

    /**
     * Form data query
     * @return array|<missing>
     */
    public function formData(): array
    {
        $result = [];
        try {
            $columns = $this->commaColumns($this->form);
            $result = db()
                ->run(
                    "SELECT $columns FROM $this->table_name WHERE $this->primary_key = ?",
                    [$this->model_id]
                )
                ->fetch(PDO::FETCH_ASSOC);
            // Return an empty form if there is no result
            if (!$result) {
                return [];
            }
        } catch (Exception $ex) {
            if (!app()->isDebug()) {
                app()->serverError();
            }
        }
        return $result;
    }

    /**
     * Module update method
     */
    public function update(): bool
    {
        $values = [...$this->formRequestValues(), $this->model_id];
        $columns = $this->placeholderColumns($this->form);
        $result = db()->query(
            "UPDATE $this->table_name SET $columns WHERE $this->primary_key = ?",
            ...$values
        );
        return $result ? true : false;
    }

    /**
     * Module insert method
     */
    public function insert(): string|false
    {
        $values = $this->formRequestValues();
        $columns = $this->placeholderColumns($this->form);
        $result = db()->query(
            "INSERT INTO $this->table_name SET $columns",
            ...$values
        );
        if ($result) {
            return db()->lastInsertId();
        }
        return false;
    }

    /**
     * Module delete method
     */
    public function delete(): bool
    {
        $result = db()->query(
            "DELETE FROM $this->table_name WHERE $this->primary_key = ?",
            $this->model_id
        );
        return $result ? true : false;
    }

    /**
     * Table view content
     */
    protected function tableContent(): array
    {
        return [
            "content" => twig("layouts/table.html", [
                "route" => $this->route,
                "primary_key" => $this->primary_key,
                "edit_route" => $this->routeName("edit"),
                "destroy_route" => $this->routeName("destroy"),
                "columns" => array_keys($this->table),
                "data" => $this->tableData(),
                "edit_enabled" => $this->edit_enabled,
                "destroy_enabled" => $this->destroy_enabled,
            ]),
        ];
    }

    /**
     * Form view content
     */
    protected function formContent(): array
    {
        return [
            "content" => twig("layouts/form.html", [
                "route" => $this->route,
                "primary_key" => $this->primary_key,
                "index_route" => $this->routeName("index"),
                "edit_route" => $this->routeName("edit"),
                "modify_route" => $this->routeName("modify"),
                "destroy_route" => $this->routeName("destroy"),
                "form" => $this->form,
                "data" => $this->formData(),
                "edit_enabled" => $this->edit_enabled,
                "destroy_enabled" => $this->destroy_enabled,
                "model_id" => $this->model_id,
            ]),
        ];
    }

    /**
     * Get the module content for the view
     */
    protected function getContent(): array
    {
        $route = app()->getRoute();
        // These are for views only
        return match ($route->getName()) {
            "module.index" => $this->tableContent(),
            "module.edit", "module.create", "module.store", "module.modify",
            "module.destroy" => $this->formContent(),
            default => throw new Error(
                "module data error: route name not defined '{$route->getName()}'"
            ),
        };
    }

    /**
     * Replaces view content by calling getContent
     * @return array<string,string>
     */
    public function content(): array
    {
        $default = [
            "route" => $this->route,
            "link" => app()->moduleRoute($this->routeName("index")),
            "parent" => $this->parent,
            "title" => $this->title,
            "sidebar" => $this->sidebar(),
            "icon" => $this->icon ?? "box",
            "content" => "",
        ];
        return array_replace($default, $this->getContent());
    }

    /**
     * Collection of modules for sidebar view
     * @return array<int,array>
     */
    protected function sidebar(): array
    {
        $config = config("paths")["modules"];
        $map = app()->classMap($config);
        $modules = [];
        foreach ($map as $class => $file) {
            $class = new $class();
            $modules[$class?->parent ?? "Adminstration"][] = [
                "link" => $class->routeName("index"),
                "title" => $class->title,
                "parent" => $class->parent,
                "icon" => $class->icon ?? "box",
            ];
        }
        return $modules;
    }
}
