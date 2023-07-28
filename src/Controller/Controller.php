<?php

namespace Nebula\Controller;

use Nebula\Interfaces\Controller\Controller as BaseController;
use Nebula\Interfaces\Http\Request;

class Controller implements BaseController
{
  public function __construct(protected Request $request)
  {
  }
}
