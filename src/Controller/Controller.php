<?php

namespace Nebula\Controller;

use Nebula\Interfaces\Controller\Controller as NebulaController;
use Nebula\Validation\Validate;

class Controller implements NebulaController
{
    // Validation errors
    protected array $errors = [];

    public function __construct()
    {
    }

    /**
     * @param array<int,mixed> $rules
     */
    protected function validate(array $rules): bool
    {
        $result = Validate::request($rules);
        $this->errors = Validate::$errors;
        return $result;
    }
}
