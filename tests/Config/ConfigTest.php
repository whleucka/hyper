<?php declare(strict_types=1);

namespace Nebula\Tests\Config;

use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
  protected $app;

  protected function setUp(): void
  {
    $this->app = require_once __DIR__ . "/../../bootstrap/app.php";
    parent::setUp();
  }
  public function test_array_fetch(): void
  {
    $db_config = config('database');
    $this->assertTrue(is_array($db_config));
  }

  public function test_single_item_fetch(): void
  {
    $db_enabled = config('database.enabled');
    $this->assertTrue(is_bool($db_enabled));
  }

  public function test_config_throw_exception_if_item_does_not_exist(): void
  {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage("Configuration doesn't exist");
    $test = \App\Config\Config::get('nonexistent');
  }

  public function test_throw_exception_if_config_does_not_exist(): void
  {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage("Configuration doesn't exist");
    $test = config('nonexistent');
  }

  public function test_throw_exception_if_config_item_does_not_exist(): void
  {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage("Configuration item doesn't exist");
    $test = config('database.nonexistent');
  }

  public function test_config_is_singleton(): void
  {
    $config1 = config('database');
    $config2 = config('database');
    $this->assertSame($config1, $config2);
  }
}
