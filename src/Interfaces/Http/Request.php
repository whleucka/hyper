<?php

namespace Nebula\Interfaces\Http;

interface Request
{
  public function getMethod(): string;
  public function getUri(): string;
}
