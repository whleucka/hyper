<?php

namespace Nebula\Http;

use Nebula\Interfaces\Http\Request as NebulaRequest;
use Nebula\Traits\Instance\Singleton;
use StellarRouter\Route;

class Request implements NebulaRequest
{
    use Singleton;

    public ?Route $route;
    private array $data;

    public function __construct()
    {
        $this->data = $this->request();
    }

    /**
     * @param mixed $name
     */
    public function __get($name): mixed
    {
        return $this->data[$name] ?? null;
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function getMethod(): string
    {
      return $this->server("REQUEST_METHOD");
    }

    public function getUri(): string
    {
      return $this->server("REQUEST_URI");
    }

    public function get(string $name): mixed
    {
       return $this->data[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return key_exists($name, $this->data);
    }

    public function server(?string $name = null): string|array
    {
        return $name ? $_SERVER[$name] : $_SERVER;
    }

    public function request(?string $name = null): mixed
    {
        return $name ? $_REQUEST[$name] : $_REQUEST;
    }

    public function post(?string $name = null): mixed
    {
        return $name ? $_POST[$name] : $_POST;
    }

    public function query(?string $name = null): mixed
    {
        return $name ? $_GET[$name] : $_GET;
    }

    public function files(?string $name = null): mixed
    {
        return $name ? $_FILES[$name] : $_FILES;
    }
}

