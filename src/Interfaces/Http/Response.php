<?php

namespace Nebula\Interfaces\Http;

interface Response
{
    public function setStatusCode(int $statusCode): void;
    public function getStatusCode(): int;
    public function getHeader(string $name): string;
    public function setHeader(string $name, string $value): void;
    public function hasHeader(string $name): bool;
    public function setContent(string $content): void;
    public function send(): void;
}
