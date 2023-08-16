<?php

namespace Nebula\Http;

use Nebula\Interfaces\Http\Response as NebulaResponse;

class Response implements NebulaResponse
{
    private mixed $content = "";

    public function setStatusCode(int $statusCode = 200): void
    {
        http_response_code($statusCode);
    }

    public function getHeader(string $name): string
    {
        return getallheaders()[$name];
    }

    public function setHeader(string $name, string $value): void
    {
        header("$name: $value");
    }

    public function setContent(mixed $content): void
    {
        $this->content = $content;
    }

    public function getContent(): mixed
    {
        return $this->content;
    }

    public function send(): void
    {
        echo $this->content;
    }

    public function getStatusCode(): int
    {
        return http_response_code();
    }

    public function hasHeader(string $name): bool
    {
        return preg_match("/$name/", implode(";", headers_list()));
    }
}
