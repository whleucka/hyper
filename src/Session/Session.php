<?php

namespace Nebula\Session;

use Nebula\Traits\Instance\Singleton;

class Session
{
    use Singleton;

    private $data = [];

    public function get(string $name)
    {
        @session_start();
        session_write_close();
        $this->data = $_SESSION;
        return $this->data[$name] ?? null;
    }

    public function set(string $name, mixed $value)
    {
        @session_start();
        $this->data[$name] = $value;
        $_SESSION = $this->data;
        session_write_close();
    }

    public function getAll()
    {
        @session_start();
        session_write_close();
        return $_SESSION;
    }

    public function destroy()
    {
        @session_start();
        $_SESSION = $this->data = [];
        session_destroy();
        session_write_close();
    }
}
