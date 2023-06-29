<?php

namespace Nebula\Controllers;

use GalaxyPDO\DB;
use Nebula\Container\Container;
use Nebula\Validation\Validate;
use stdClass;

class Controller
{
    protected Container $container;
    protected array $request;

    public function __construct()
    {
        $this->request = $this->filterRequest();
    }

    /**
     * Get PDO database connection
     */
    public function db(): DB
    {
        return app()->getDatabase();
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
     * @param array<int,mixed> $rules
     */
    protected function validate(array $rules): ?stdClass
    {
        return Validate::request($this->request, $rules);
    }
}
