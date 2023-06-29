<?php

namespace Nebula\Session;

class Session
{
  private $data;

  public function get(string $name)
  {
    @session_start();
    session_write_close();
    $this->data = $_SESSION;
    return $this->data[$name] ?? null;
  }

  public function set(string $name, string $value)
  {
    @session_start();
    $this->data[$name] = $value;
    $_SESSION = $this->data;
    session_write_close();
  }
}
