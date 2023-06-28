<?php

namespace Nebula\Controllers;

use GalaxyPDO\DB;
use Nebula\Container\Container;
use Nebula\Validation\Validate;
use stdClass;

class Controller
{
    protected DB $db;
    protected Container $container;
    protected array $request;

    public function __construct()
    {
        $this->request = $this->filterRequest();
    }

    /**
     * Get the DI container
     */
    public function container(): Container
    {
        return Container::getInstance();
    }

    /**
     * Get database connection
     */
    public function db(): DB
    {
        return $this->container()->get(DB::class);
    }

    /**
     * Filter the request to only contain data from POST, GET, and FILES
     */
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

    /**
     * Validate the controller request based on an array of rules
     * If there is a validation error, then you will recieve null
     */
    protected function validate(array $rules): ?stdClass
    {
        return Validate::request($this->request, $rules);
    }
}
