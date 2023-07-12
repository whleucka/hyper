<?php

namespace Nebula\Controllers\Admin\Modules;

use Error;
use PDO;

class Module
{
    protected array $table = [];
    protected array $form = [];

    /**
     * @param array<int,mixed> $config
     */
    public function __construct(protected array $config)
    {
    }

    public function __get(string $name): mixed
    {
        return $this->config[$name];
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value): void
    {
        $this->config[$name] = $value;
    }

    protected function setColumns(array $columns): string
    {
        $stmt = array_map(fn ($column) => $column . " = ?", $columns);
        return implode(", ", $stmt);
    }

    protected function tableData(string $table_name)
    {
        $columns = implode(", ", array_values($this->table));
        return db()->run("SELECT $columns FROM $table_name")->fetchAll(PDO::FETCH_ASSOC);
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
                "columns" => array_keys($this->table),
                "data" => isset($this->config['table']) ? $this->tableData($this->config["table"]) : []
            ])
        ];
    }

    protected function form(): array
    {
        return [
            "content" => twig("layouts/form.html", [])
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
            default => throw new Error("module name doesn't exist")
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
                ];
            }
        }
        return $modules;
    }
}
