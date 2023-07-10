<?php

namespace Nebula\Controllers\Admin\Modules;

class Profile extends Module
{
  public function __construct()
  {
    parent::__construct('profile', 'Profile');
  }

  protected function twigData(): array
  {
    // This is how to pass data to twig view from module
    return [...parent::twigData(), 'title' => 'Profile'];
  }
}
