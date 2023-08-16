<?php

namespace Nebula\Model;

use Nebula\Interfaces\Database\Factory as NebulaFactory;

class Factory implements NebulaFactory
{
    protected string $model = Model::class;

    /**
     * Make a single model or an array of models
     * @param array<int,mixed> $data
     */
    public function make(
        ?array $data = null,
        int $n = 1,
        bool $save = false,
        bool $mock = false
    ): mixed {
        if (empty($data) || is_null($data)) {
            $data = $this->default();
        }
        $models = [];
        for ($i = 0; $i < $n; $i++) {
            $model = $this->new($data, $mock);
            if ($save) {
                $model->save();
            }
            $models[] = $model;
        }
        return $models;
    }

    /**
     * Create a single model
     * @param array<int,mixed> $data
     */
    public function new(?array $data, bool $mock = false): Model
    {
        $model = new $this->model();
        if ($mock) {
            $data = $this->mock();
        } elseif (is_null($data)) {
            $data = [];
        }
        foreach ($data as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }

    /**
     * Default model implementation
     */
    public function default(): array
    {
        return [];
    }

    /**
     * Mock model implementation
     */
    public function mock(): array
    {
        return [];
    }
}
