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
        $token = session()->get("csrf_token");
        $input = <<<EOT
      <input type="hidden" name="csrf_token" value="$token">
EOT;
        return $input;
    }

    public function dump(...$args): void
    {
        dump(...$args);
    }
}
