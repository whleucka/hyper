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
    /** Table/Form Meta */
    public array $table = [];
    public array $form = [];
    public array $controls = [];
    public array $search = [];
    /** Pagination */
    public int $per_page = 5;
    public int $page = 1;
    public int $total_pages = 1;
    /** Query meta */
    public array $joins = [];
    public array $where = [];
    public array $parameters = [];
    public array $having = [];
    public array $group_by = [];
    public string $order_by;
    public string $sort = "DESC";
    /** Validation rules */
    public array $validation = [];
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
     * Add to where clause
     * @param mixed $params
     */
    public function where(string $condition, ...$params): Module
    {
        $this->where[] = $condition;
        foreach ($params as $param) {
            $this->parameters[] = $param;
        }
        return $this;
    }

    /**
     * Add join clause
     */
    public function join(string $clause): Module
    {
        $this->joins[] = $clause;
        return $this;
    }

    /**
     * Add having clause
     */
    public function having(string $clause): Module
    {
        $this->having[] = $clause;
        return $this;
    }

    /**
     * Add group by clause
     */
    public function groupBy(string $clause): Module
    {
        $this->group_by[] = $clause;
        return $this;
    }

    /**
     * Set order by column
     */
    public function orderBy(string $column): Module
    {
        $this->order_by = $column;
        return $this;
    }

    /**
     * Set sort direction
     */
    public function sort(string $direction): Module
    {
        $this->sort = $direction;
        return $this;
    }

    /**
     * Add column to table array
     */
    public function column(string $column, string $title): Module
    {
        $this->table[$column] = $title;
        return $this;
    }

    /**
     * Add control to form array
     */
    public function control(string $column, string $title, string $type = 'input'): Module
    {
        $this->form[$column] = $title;
        $this->controls[$column] = $type;
        return $this;
    }

    /**
     * Add column to search array
     */
    public function search(string $column): Module
    {
        $this->search[] = $column;
        return $this;
    }

    /**
     * Add rule to validation array
     * @param mixed $column
     * @param array<int,mixed> $rule
     */
    public function rule($column, array $rule): Module
    {
        $this->validation[$column] = $rule;
        return $this;
    }

    /**
     * Return table query
     */
    public function getTableQuery(): string
    {
        $columns = $this->commaColumns(array_keys($this->table));
        $joins = implode(" ", $this->joins);
        $query = "SELECT $columns FROM $this->table_name $joins";

        // Build where clause
        if (!empty($this->where)) {
            $query .= 'WHERE (' . implode(") AND (", $this->where) . ') ';
        }
        // Build group by glause
        if (!empty($this->group_by)) {
            $group_by = implode(", ", $this->group_by);
            $query .= "GROUP BY $group_by ";
        }
        // Build having clause
        if (!empty($this->having)) {
            $having = '(' . implode(") AND (", $this->having) . ')';
            $query .= "HAVING $having ";
        }
        // Default order by
        if (!isset($this->order_by)) {
            $this->order_by = $this->primary_key;
        }
        // Order by clause
        $query .= "ORDER BY $this->order_by $this->sort";

        return $query;
    }

    /**
     * Table data query
     */
    public function tableData(): array
    {
        $result = [];
        try {
            $result = db()
                ->run($this->getTableQuery(), $this->parameters)
                ->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            if (!app()->isDebug()) {
                app()->serverError();
            }
        }
        return $result;
    }

    public function getFormQuery(): string
    {
        $columns = $this->commaColumns(array_keys($this->form));
        return "SELECT $columns FROM $this->table_name WHERE $this->primary_key = ?";
    }

    /**
     * Form data query
     * @return array|<missing>
     */
    public function formData(): array
    {
        $result = [];
        try {
            $result = db()
                ->run(
                    $this->getFormQuery(),
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
                if ($new_value != $old->$column) {
                    Audit::insert(user()->id, $this->table_name, $this->id, $column, $old->$column, $new_value, 'UPDATE');
                }
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
            "module.index" => $this->tableView(),
            "module.edit", "module.create", "module.store", "module.modify",
            "module.destroy" => $this->formView(),
            default => throw new Error(
                "module data error: route name undefined '{$route->getName()}'"
            ),
        };
    }

    public function tableView(): string
    {
        $data = $this->tableData();
        return twig("layouts/table.html", [
            ...$this->sharedDefaults(),
            "columns" => array_values($this->table),
            "data" => $data,
        ]);
    }

    public function formView(): string
    {
        $data = $this->formData();
        return twig("layouts/form.html", [
            ...$this->sharedDefaults(),
            "form" => $this->form,
            "data" => $data,
            "id" => $this->id,
            "controls" => $this->controls($data),
        ]);
    }
    /**
     * @param mixed $form_data
     */
    public function controls($form_data): array
    {
        $controls = [];
        foreach ($this->form as $column => $title) {
            if (isset($this->controls[$column])) {
                $value = $form_data[$column] ?? '';
                $controls[$column] = match ($this->controls[$column]) {
                    "floating_input" => Controls::floatingInput($column, $title, $value),
                    "floating_textarea" => Controls::floatingTextarea($column, $title, $value),
                    "textarea" => Controls::textarea($column, $title, $value),
                    "input" => Controls::input($column, $title, $value),
                    "number" => Controls::number($column, $title, $value),
                    "checkbox" => Controls::checkbox($column, $title, $value),
                    "password" => Controls::password($column, $title, $value),
                    default => Controls::readonly($title, $value),
                };
            }
        }
        return $controls;
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
            "parent" => $this->parent ?? 'Administration',
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
     * @param mixed $return_array
     */
    protected function placeholderColumns(array $columns, $return_array = false): string|array
    {
        $stmt = array_map(fn ($column) => $column . " = ?", $columns);
        return $return_array ? $stmt : $this->commaColumns($stmt);
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
                fn ($column) => request()->get($column) ?? null,
                array_keys($this->form)
            )
        );
    }
}
