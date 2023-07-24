<?php

namespace Nebula\Util;

use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;

/**
 * Register functions to be used in twig templates
 */
class TwigExtension extends AbstractExtension implements ExtensionInterface
{
    /**
     * Functions passed to twig
     */
    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction("csrf", [$this, "csrf"]),
            new \Twig\TwigFunction("moduleRoute", [$this, "moduleRoute"]),
            new \Twig\TwigFunction("findRoute", [$this, "findRoute"]),
            new \Twig\TwigFunction("buildRoute", [$this, "buildRoute"]),
            new \Twig\TwigFunction("old", [$this, "old"]),
            new \Twig\TwigFunction("dump", [$this, "dump"]),
        ];
    }

    public function dump(mixed $stuff) {
        dump($stuff);
    }

    /**
     * Embed a CSRF hidden input
     */
    public function csrf(): string
    {
        $token = session()->get("csrf_token");
        $input = <<<EOT
<input type="hidden" name="csrf_token" value="{$token}">
EOT;
        return $input;
    }

    /**
     * Find a route by name
     */
    public function findRoute(string $name): string
    {
        $route = app()->findRoute($name);
        if (!is_null($route)) {
            return $route->getPath();
        }
        return "";
    }

    /**
     * Find a module route
     */
    public function moduleRoute(string $name, ...$args): string
    {
        $route = app()->moduleRoute($name, ...$args);
        if (!is_null($route)) {
            return $route;
        }
        return "";
    }

    /**
     * Build a route from args
     * @param mixed $args
     */
    public function buildRoute(string $name, ...$args): string
    {
        $route = app()->buildRoute($name, ...$args);
        if (!is_null($route)) {
            return $route;
        }
        return "";
    }

    /**
     * Use value from request for form input, etc
     */
    public function old(?string $name): mixed
    {
        return request()->get($name ?? "");
    }
}
