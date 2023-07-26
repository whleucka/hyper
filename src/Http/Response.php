<?php

namespace Nebula\Http;

use Nebula\Interfaces\Http\Response as NebulaResponse;

class Response implements NebulaResponse
{
    private string $content = '';

    public function setStatusCode(int $statusCode = 200): void
    {
        http_response_code($statusCode);
    }

    public function setHeader(string $name, string $value): void
    {
      header("$name: $value");
    }

    public function setContent(string $content): void
    {
      $this->content = $content;
    }

    public function send(): void
    {
      echo $this->content;
    }
}

