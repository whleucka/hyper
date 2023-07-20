<?php

declare(strict_types=1);

use Nebula\Session\Flash;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../src/Util/functions.php";

final class FlashTest extends TestCase
{
  protected function setUp(): void
  {
    app()->init();
  }

  public function test_a_empty_message_array(): void
  {
    $this->assertFalse(Flash::hasMessages());
  }

  public function test_add_message(): void
  {
    Flash::addMessage("success", "you're amazing");
    $this->assertSame(session()->get("flash"), ['type' => "success", 'message' => "you're amazing"]);
  }

  public function test_has_message(): void
  {
    Flash::addMessage("success", "you're amazing");
    $this->assertTrue(Flash::hasMessages());
  }

  public function test_get_message(): void
  {
    Flash::addMessage("success", "you're amazing");
    $messages = Flash::getMessages();
    $this->assertNotEmpty($messages);
    // The flash array should be empty or null
    $this->assertEmpty(session()->get("flash"));
  }
}

