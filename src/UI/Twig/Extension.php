<?php

namespace Nebula\UI\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;

class Extension extends AbstractExtension implements ExtensionInterface
{
    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction("dump", [$this, "dump"]),
            new \Twig\TwigFunction("csrf", [$this, "csrf"]),
        ];
    }

    public function csrf(): string
    {
        return csrf();
    }

    public function dump(...$args): void
    {
        dump(...$args);
    }
}
