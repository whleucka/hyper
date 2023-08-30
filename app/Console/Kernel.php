<?php

namespace App\Console;

use Nebula\Console\Kernel as ConsoleKernel;
use App\Soprano\Music\Scan;

final class Kernel extends ConsoleKernel
{

  public function __construct()
  {
    // You can register new console commands like this
    // If you're not familiar with PHP getopt, you 
    // can read more about it here: 
    // https://www.php.net/manual/en/function.getopt.php
    $this->registerCommand('short', 'v', "Display version", fn() => $this->version());
    $this->registerCommand('long', 'version', "Display version", fn() => $this->version());

    $this->registerCommand('long', 'scan-music', "Scan music library. Usage scan-music=/music/directory", function(string $directory) {
      print_r($directory);die;
      // $scanner = new Scan();
      // print_r($scanner->findFiles($directory));
    });
  }

  public function version(): void
  {
    $this->write("v0.0.1");
  }
}
