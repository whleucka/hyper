<?php

namespace Nebula\Util;

use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;

class TwigExtension extends AbstractExtension implements ExtensionInterface
{
    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction("csrf", [$this, "csrf"]),
            new \Twig\TwigFunction("findRoute", [$this, "findRoute"]),
            new \Twig\TwigFunction("buildRoute", [$this, "buildRoute"]),
        ];
    }

    public function csrf(): string
    {
        $token = session()->get("csrf_token");
        $input = <<<EOT
<input type="hidden" name="csrf_token" value="{$token}">
EOT;
        return $input;
    }

    public function findRoute(string $name): string
    {
        $route = app()->findRoute($name);
        if (!is_null($route)) {
            return $route->getPath();
        }
        return "";
    }

    public function buildRoute(string $name, ...$args): string
    {
        $route = app()->buildRoute($name, ...$args);
        if (!is_null($route)) {
            return $route;
        }
        return "";
    }
}
