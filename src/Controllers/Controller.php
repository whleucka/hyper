<?php

namespace Nebula\Controllers;

use GalaxyPDO\DB;
use Nebula\Container\Container;
use Twig;

class Controller
{
    protected DB $db;
    protected Container $container;

    public function __construct()
    {
        $this->container = Container::getInstance();
        $this->db = $this->container->get(DB::class);
    }

    /**
     * Render a twig template
     * @param array<int,mixed> $data
     */
    protected function render(string $path, array $data = []): string
    {
        $twig = $this->container->get(Twig\Environment::class);
        return $twig->render($path, $data);
    }
}
