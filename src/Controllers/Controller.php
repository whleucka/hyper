<?php

namespace Nebula\Controllers;

use Nebula\Container\Container;
use Nebula\Validation\Validate;
use Symfony\Component\HttpFoundation\Request;
use stdClass;

class Controller
{
    protected Container $container;
    protected Request $request;

    public function __construct()
    {
        $this->request = request();
    }

    /**
     * Filter the request to only contain data from POST, GET, and FILES
     * @return array<int,mixed>
     */
    protected function filterRequest(): array
    {
        return [
            ...$this->request->request,
            ...$this->request->query,
            ...$this->request->files,
        ];
    }

    /**
     * Validate the controller request based on an array of rules
     * If there is a validation error, then you will recieve null
     * @param array<int,mixed> $rules
     */
    protected function validate(array $rules): ?stdClass
    {
        return Validate::request($this->filterRequest(), $rules);
    }
}
