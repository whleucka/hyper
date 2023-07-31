<?php

namespace Nebula\Session;

use Nebula\Interfaces\Session\Session as NebulaSession;

class Session implements NebulaSession
{
    private $data = [];

    public function get(string $name): mixed
    {
        @session_start();
        session_write_close();
        $this->data = $_SESSION;
        return $this->data[$name] ?? null;
    }

    public function set(string $name, mixed $value): void
    {
        @session_start();
        $this->data[$name] = $value;
        $_SESSION = $this->data;
        session_write_close();
    }

    public function remove(string $name): void
    {
        @session_start();
        unset($this->data[$name]);
        $_SESSION = $this->data;
        session_write_close();
    }

    public function has(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function getAll(): array
    {
        @session_start();
        session_write_close();
        return $_SESSION;
    }

    public function destroy(): void
    {
        @session_start();
        $_SESSION = $this->data = [];
        session_destroy();
        session_write_close();
    }
}
