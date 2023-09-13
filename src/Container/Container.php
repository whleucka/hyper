<?php

namespace Nebula\Container;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class Container
{
    private ?ContainerInterface $container = null;
    private ContainerBuilder $builder;

    public function build(): void
    {
        $this->builder = new ContainerBuilder();
        $this->builder->addDefinitions(config("container"));
        $this->container = $this->builder->build();
    }

    /**
     * The container get method is explicitly defined
     * here to avoid LSP diagnostic errors
     */
    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    /**
     * Otherwise, we can call any method from the
     * container class
     */
    public function __call($method, $args)
    {
        return $this->container?->$method(...$args);
    }
}
