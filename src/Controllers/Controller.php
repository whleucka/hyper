<?php

namespace Nebula\Controllers;

use GalaxyPDO\DB;

class Controller
{
    public function __construct(protected DB $db)
    {
    }
}
