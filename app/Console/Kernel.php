<?php

namespace App\Console;

use Nebula\Console\Kernel as ConsoleKernel;

final class Kernel extends ConsoleKernel
{

  public function __construct()
  {
    // You can register new console commands like this
    // If you're not familiar with PHP getopt, you 
    // can read more about it here: 
    // https://www.php.net/manual/en/function.getopt.php
    $this->registerCommand('short', 'v', "Display version.", fn() => $this->version());
    $this->registerCommand('long', 'version', "Display version.", fn() => $this->version());
  }

  protected function version(): void
  {
    $this->write("v0.0.1");
  }
}
