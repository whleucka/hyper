<?php

namespace Nebula\Interfaces\Database;

use Nebula\Interfaces\Model\Model;

interface Factory
{
    public function make(
        ?array $data = null,
        int $n = 1,
        bool $save = false,
        bool $mock = false
    ): mixed;
    public function new(?array $data, bool $mock = false): Model;
    public function default(): array;
    public function mock(): array;
}
