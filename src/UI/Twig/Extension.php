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
    ];
  }
  
  public function dump(...$args)
  {
    dump(...$args);
  }
}
