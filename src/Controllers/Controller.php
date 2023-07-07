<?php

namespace Nebula\Controllers;

use Nebula\Validation\Validate;

class Controller
{
    /**
     * Validate the controller request based on an array of rules
     * @param array<int,mixed> $rules
     */
    protected function validate(array $rules): bool
    {
        return Validate::request($rules);
    }
}
