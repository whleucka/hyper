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

    public function __call($method, $args)
    {
        return $this->container?->$method(...$args);
    }
}
