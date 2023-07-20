<?php

namespace Nebula\Admin;

use Error;
use Exception;
use PDO;

class Module
{
    /** Queries */
    private ?string $id;
    public string $primary_key = "id";
    public string $table_name = "";
    public array $table = [];
    public array $form = [];
    /** Validation */
    public array $create_validation = [];
    public array $modify_validation = [];
    /** Permissions */
    public $create_enabled = true;
    public $edit_enabled = true;
    public $destroy_enabled = true;
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

    public function __construct(?string $id = null)
    {
        // This is the id (primary key) value for the module queries
        $this->id = $id;
        // Defaults - if these variables aren't set, then we assume route name
        if (!$this->table_name) {
            $this->table_name = strtolower($this->route);
        }
        if (!$this->title) {
            $this->title = ucfirst($this->route);
        }
    }

    /**
     * Return validation array by route type
     */
    public function validationArray(string $route_type): array
    {
        return match ($route_type) {
            "create" => $this->create_validation,
            "modify" => $this->modify_validation,
            default => throw new Error("unknown validation route type"),
        };
    }

    /**
     * Table data query
     */
    public function tableData(): array
    {
        $result = [];
        try {
            $columns = $this->commaColumns(array_keys($this->table));
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
            $columns = $this->commaColumns(array_keys($this->form));
            $result = db()
                ->run(
                    "SELECT $columns FROM $this->table_name WHERE $this->primary_key = ?",
                    [$this->id]
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
        $request_values = $this->formRequestValues();
        $old = db()->selectOne("SELECT * FROM $this->table_name WHERE $this->primary_key = ?", $this->id);
        $values = [...$request_values, $this->id];
        $columns = $this->placeholderColumns(array_keys($this->form));
        $result = db()->query(
            "UPDATE $this->table_name SET $columns WHERE $this->primary_key = ?",
            ...$values
        );
        if ($result) {
            foreach (array_keys($this->form) as $i => $column) {
                $new_value = $request_values[$i];
                if ($new_value != $old->$column)
                Audit::insert(user()->id, $this->table_name, $this->id, $column, $old->$column, $new_value, 'UPDATE');
            }
        }
        return $result ? true : false;
    }

    /**
     * Module insert method
     */
    public function insert(): string|false
    {
        $request_values = $this->formRequestValues();
        $columns = $this->placeholderColumns(array_keys($this->form));
        $result = db()->query(
            "INSERT INTO $this->table_name SET $columns",
            ...$request_values
        );
        if ($result) {
            $id = db()->lastInsertId();
            foreach (array_keys($this->form) as $i => $column) {
                $new_value = $request_values[$i];
                Audit::insert(user()->id, $this->table_name, $id, $column, null, $new_value, 'INSERT');
            }
            return $id;
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
            $this->id
        );
        if ($result) {
            Audit::insert(user()->id, $this->table_name, $this->id, $this->primary_key, $this->id, null, 'DELETE');
        }
        return $result ? true : false;
    }

    /**
     * Return a twig view for index / edit / create views
     */
    protected function view(): string
    {
        $route = app()->getRoute();
        return match ($route->getName()) {
            "module.index" => twig("layouts/table.html", [
                ...$this->sharedDefaults(),
                "columns" => array_values($this->table),
                "data" => $this->tableData(),
            ]),
            "module.edit", "module.create", "module.store", "module.modify",
            "module.destroy" => twig("layouts/form.html", [
                ...$this->sharedDefaults(),
                "form" => $this->form,
                "data" => $this->formData(),
                "id" => $this->id,
            ]),
            default => throw new Error(
                "module data error: route name undefined '{$route->getName()}'"
            ),
        };
    }

    /**
     * Module view data
     * @return array<mixed,mixed>
     */
    public function data(): array
    {
        return [
            ...$this->sharedDefaults(),
            "link" => app()->moduleRoute($this->routeName("index")),
            "parent" => $this->parent,
            "title" => $this->title,
            "sidebar" => $this->sidebar(),
            "icon" => $this->icon ?? "box",
            "content" => $this->view(),
        ];
    }

    /**
     * Collection of modules for sidebar view
     * @return array<int,array>
     */
    private function sidebar(): array
    {
        $config = config("paths")["modules"];
        $map = app()->classMap($config);
        $modules = [];
        foreach ($map as $class => $file) {
            $class = new $class();
            $modules[$class?->parent ?? "Administration"][] = [
                "link" => $class->routeName("index"),
                "title" => $class->title,
                "parent" => $class->parent,
                "icon" => $class->icon ?? "box",
            ];
        }
        return $modules;
    }

    /**
     * Return the shared default data values array
     * @return array<string,mixed>
     */
    public function sharedDefaults(): array
    {
        return [
            "primary_key" => $this->primary_key,
            "route" => $this->route,
            "create_enabled" => $this->create_enabled,
            "destroy_enabled" => $this->destroy_enabled,
            "edit_enabled" => $this->edit_enabled,
            "index_route" => $this->routeName("index"),
            "edit_route" => $this->routeName("edit"),
            "modify_route" => $this->routeName("modify"),
            "destroy_route" => $this->routeName("destroy"),
        ];
    }

    /**
     * Build a route name by type (index, edit, create, etc)
     */
    public function routeName(string $type): string
    {
        return $this->route . ".$type";
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
     * Return array of form request values
     * Entity attributes will be updated from the request
     * as long as they are defined in $this->form
     */
    protected function formRequestValues(): array
    {
        return array_values(
            array_map(
                fn($column) => request()->get($column) ?? null,
                array_keys($this->form)
            )
        );
    }
}