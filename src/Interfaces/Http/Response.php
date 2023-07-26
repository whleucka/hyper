<?php

namespace Nebula\Interfaces\Http;

interface Response
{
  public function setStatusCode(int $statusCode): void;
  public function setHeader(string $name, string $value): void;
  public function setContent(string $content): void;
  public function send(): void;
}
