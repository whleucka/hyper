<?php

namespace Nebula\Controllers\Admin\Modules;

use Nebula\Util\TwigExtension;
use PDO;

class Module
{
    protected $table;
    protected $form;

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

    public function getFormattedColumns(array $columns): string
    {
        $stmt = array_map(fn($column) => $column . " = ?", $columns);
        return implode(", ", $stmt);
    }

    public function tableData(string $table_name)
    {
        $columns = implode(", ", array_values($this->table));
        return db()->run("SELECT $columns FROM $table_name")->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get data for the twig template
     * @return array<string,string>
     */
    public function getData(): array
    {
        return $this->mergeData();
    }

    /**
     * Override function for twig data
     */
    protected function data(): array
    {
        return [];
    }

    /**
     * Merge default twig data and data()
     * @return array<string,string>
     */
    private function mergeData(): array
    {
        $te = new TwigExtension();
        $default = [
            "route" => $this->route,
            "link" => $te->moduleRoute($this->route . ".index"),
            "parent" => $this->parent,
            "title" => $this->title,
            "modules" => $this->getModules(),
            "content" => "",
        ];
        return array_replace($default, $this->data());
    }

    /**
     * @return array<int,array>
     */
    public function getModules(): array
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
