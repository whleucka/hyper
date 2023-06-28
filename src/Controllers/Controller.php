<?php

namespace Nebula\Controllers;

use GalaxyPDO\DB;
use Nebula\Container\Container;

class Controller
{
    protected DB $db;
    protected Container $container;
    protected array $request;

    public function __construct()
    {
        $this->request = $this->filterRequest();
    }

    public function container(): Container
    {
        return Container::getInstance();
    }

    public function db(): DB
    {
        return $this->container()->get(DB::class);
    }

    protected function filterRequest(): array
    {
        $request = app()->getRequest();
        $filtered_request = [
            ...$request->request,
            ...$request->query,
            ...$request->files,
        ];
        return $filtered_request;
    }
}
