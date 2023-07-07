<?php

namespace Nebula\Controllers;

use Nebula\Container\Container;
use Nebula\Validation\Validate;

class Controller
{
    protected Container $container;
    /**
     * Validate the controller request based on an array of rules
     * If there is a validation error, then you will recieve null
     * @param array<int,mixed> $rules
     */
    protected function validate(array $rules): bool
    {
        return Validate::request($rules);
    }
}
