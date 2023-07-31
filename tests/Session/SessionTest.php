<?php

declare(strict_types=1);

namespace Nebula\Tests\Session;

use Nebula\Session\Session;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
  public function test_set_session_var(): void
  {
    $session = new Session;
    $session->set("test", "bork");
    $this->assertTrue(key_exists('test', $session->getAll()));
  }
  public function test_get_session_var(): void
  {
    $session = new Session;
    $session->set("test", "bork");
    $this->assertSame('bork', $session->get('test'));
  }
  public function test_has_session_var(): void
  {
    $session = new Session;
    $exists = $session->has('test');
    $this->assertFalse($exists);

    $session->set("test", "bork");
    $all = $session->getAll();
    $this->assertSame('bork', $all['test']);
  }
  public function test_remove_session_var(): void
  {
    $session = new Session;
    $session->set("test", "bork");
    $this->assertTrue(key_exists('test', $session->getAll()));
    $session->remove('test');
    $this->assertEmpty($session->getAll());
  }
  public function test_destroy_session(): void
  {
    $session = new Session;
    $session->set("test", "bork");
    $this->assertTrue(key_exists('test', $session->getAll()));
    $session->destroy();
    $this->assertEmpty($session->getAll());
  }
}
