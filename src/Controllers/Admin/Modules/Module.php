<?php

namespace Nebula\Controllers\Admin\Modules;

class Module
{
    /**
     * @param array<int,mixed> $config
     */
    public function __construct(
        protected array $config,
    ) {
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
        $default = [
            "title" => $this->title,
            "modules" => $this->getModules(),
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
                $modules[] = [
                    "route" => $class->route . ".index",
                    "title" => $class->title,
                    "icon" => $class->icon,
                ];
            }
        }
        return $modules;
    }
}
