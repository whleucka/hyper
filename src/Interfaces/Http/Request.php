<?php

namespace Nebula\Interfaces\Http;

interface Request
{
  public function getMethod(): string;
  public function getUri(): string;
  public function server(?string $name = null): mixed;
  public function request(?string $name = null): mixed;
  public function post(?string $name = null): mixed;
  public function query(?string $name = null): mixed;
  public function files(?string $name = null): mixed;
}
