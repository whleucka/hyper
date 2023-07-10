<?php

namespace Nebula\Controllers\Admin\Modules;

class Module
{
    public function __construct(private string $route, private string $title, private $icon = '')
    {
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
            "title" => $this->getTitle(),
            "modules" => $this->getModules(),
        ];
        return array_replace($default, $this->data());
    }

    /**
     * Get the module route
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Get the module title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getModules(): array
    {
        $config = config("paths")["modules"];
        $map = app()->classMap($config);
        $modules = [];
        foreach ($map as $class => $file) {
            if ($class != Module::class) {
                $class = new $class;
                $modules[] = [
                    "route" => $class->getRoute().'.index', 
                    "title" => $class->getTitle(),
                    "icon" => $class->getIcon(),
                ];
            }
        }
        return $modules;
    }
}
