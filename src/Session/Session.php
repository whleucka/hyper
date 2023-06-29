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

  public function getAll()
  {
    @session_start();
    session_write_close();
    return $_SESSION;
  }

  public function destroy()
  {
    @session_start();
    @session_destroy();    
    session_write_close();
  }
}
