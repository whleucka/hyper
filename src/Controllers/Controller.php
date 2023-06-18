<?php

namespace Nebula\Controllers;

use GalaxyPDO\DB;
use Nebula\Config\Paths;
use Twig;

class Controller
{
    public function __construct(protected ?DB $db)
    {
    }

    /**
     * @param array<int,mixed> $data
     */
    protected function render(string $path, array $data = []): string
    {
        $paths = new Paths();
        $views = $paths->getViews();
        $loader = new Twig\Loader\FilesystemLoader($views["paths"]);
        $twig = new Twig\Environment($loader, [
            "cache" => $views["cache"],
            "auto_reload" => strtolower($_ENV["APP_DEBUG"]) === "true",
        ]);
        return $twig->render($path, $data);
    }
}
