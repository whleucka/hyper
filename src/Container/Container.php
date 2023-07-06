<?php

namespace Nebula\Container;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

/**
 * Dependency injection container
 */
class Container
{
    protected static $instance;
    private array|string $definitions = [];
    private ContainerInterface $container;
    private ContainerBuilder $builder;

    public static function getInstance(): static
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function build(): void
    {
        $this->builder = new ContainerBuilder();
        if (!empty($this->definitions)) {
            $this->builder->addDefinitions($this->definitions);
        }
        $this->container = $this->builder->build();
    }

    public function get(string $target): mixed
    {
        return $this->container?->get($target);
    }

    public function setDefinitions(array|string $defintions): Container
    {
        if (!empty($defintions) || is_string($defintions)) {
            $this->definitions = $defintions;
        }
        return $this;
    }
}
