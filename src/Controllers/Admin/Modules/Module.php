<?php

namespace Nebula\Controllers\Admin\Modules;

use Error;
use PDO;

class Module
{
    protected array $table = [];
    protected array $form = [];
    protected bool $add_enabled = true;
    protected bool $edit_enabled = true;
    protected bool $delete_enabled = true;

    /**
     * @param array<int,mixed> $config
     */
    public function __construct(protected array $config)
    {
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

    protected function selectStatement(array $columns): string
    {
        return implode(", ", array_values($columns));
    }

    protected function setColumns(array $columns): string
    {
        $stmt = array_map(fn ($column) => $column . " = ?", $columns);
        return implode(", ", $stmt);
    }

    protected function tableData(string $table_name): array
    {
        if (empty($this->table) || !isset($this->config['table'])) {
            return [];
        }
        $columns = $this->selectStatement($this->table);
        return db()
            ->run("SELECT $columns FROM $table_name")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get data for the twig template
     * @return array<string,string>
     */
    public function getData(): array
    {
        return $this->mergeData();
    }

    protected function table(): array
    {
        return [
            "content" => twig("layouts/table.html", [
                "route" => $this->route,
                "edit_route" => $this->route.'.edit',
                "delete_route" => $this->route.'.delete',
                "columns" => array_keys($this->table),
                "data" => $this->tableData($this->config["table"]),
                "edit_enabled" => $this->edit_enabled,
                "delete_enabled" => $this->delete_enabled,
            ]),
        ];
    }

    protected function form(): array
    {
        return [
            "content" => twig("layouts/form.html", []),
        ];
    }
    /**
     * Override function for twig data
     */
    protected function data(): array
    {
        $route = app()->getRoute();
        return match ($route->getName()) {
            "module.index" => $this->table(),
            "module.edit" => $this->form(),
            default => throw new Error("module name doesn't exist"),
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
            "link" => app()->moduleRoute($this->route . ".index"),
            "parent" => $this->parent,
            "title" => $this->title,
            "modules" => $this->getModules(),
            "icon" => $this->icon ?? 'box',
            "add_enabled" => $this->add_enabled,
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
            if ($class != Module::class) {
                $class = new $class();
                $modules[$class?->parent ?? "Adminstration"][] = [
                    "route" => $class->route . ".index",
                    "title" => $class->title,
                    "parent" => $class->parent,
                    "icon" => $class->icon ?? 'box',
                ];
            }
        }
        return $modules;
    }
}
