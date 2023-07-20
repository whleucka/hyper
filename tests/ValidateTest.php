<?php


declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Nebula\Validation\Validate;

require_once __DIR__ . "/../src/Util/functions.php";

final class ValidateTest extends TestCase
{

  protected function setUp(): void
  {
    app()->init();
    Validate::$errors = [];
  }

  public function test_string_rule(): void
  {
    request()->request->add([
      "test" => "this is a string",
    ]);
    $this->assertTrue(
      Validate::request([
        "test" => ["string"],
      ])
    );
    request()->request->add([
      "test" => [1,2,3,4],
    ]);
    $this->assertFalse(
      Validate::request([
        "test" => ["string"],
      ])
    );
    request()->request->add([
      "test" => 1,
    ]);
    $this->assertFalse(
      Validate::request([
        "test" => ["string"],
      ])
    );
  }

  public function test_numeric_rule(): void
  {
    request()->request->add([
      "test" => 1234,
    ]);
    $this->assertTrue(
      Validate::request([
        "test" => ["numeric"],
      ])
    );
    request()->request->add([
      "test" => [1,2,3],
    ]);
    $this->assertFalse(
      Validate::request([
        "test" => ["numeric"],
      ])
    );
    request()->request->add([
      "test" => "1",
    ]);
    $this->assertFalse(
      Validate::request([
        "test" => ["numeric"],
      ])
    );
  }

  public function test_email_rule(): void
  {
    request()->request->add([
      "test" => "test@example.com",
    ]);
    $this->assertTrue(
      Validate::request([
        "test" => ["email"],
      ])
    );
    request()->request->add([
      "test" => "test@test",
    ]);
    $this->assertFalse(
      Validate::request([
        "test" => ["email"],
      ])
    );
    request()->request->add([
      "test" => "hello, world",
    ]);
    $this->assertFalse(
      Validate::request([
        "test" => ["email"],
      ])
    );
  }

  public function test_required_rule(): void
  {
    request()->request->add([
      "test" => "test@example.com",
    ]);
    $this->assertTrue(
      Validate::request([
        "test" => ["required"],
      ])
    );
    request()->request->remove("test");
    $this->assertFalse(
      Validate::request([
        "test" => ["required"],
      ])
    );
  }

  public function test_match_rule(): void
  {
    request()->request->add([
      "test" => "test@example.com",
      "test_check" => "test@example.com",
    ]);
    $this->assertTrue(
      Validate::request([
        "test" => ["email"],
        "test_check" => ["email", "match"],
      ])
    );
    request()->request->add([
      "test_check" => "test",
    ]);
    $this->assertFalse(
      Validate::request([
        "test" => ["email"],
        "test_check" => ["email", "match"],
      ])
    );
  }

  public function test_min_length_rule(): void
  {
    request()->request->add([
      "test" => "testing",
    ]);
    $this->assertTrue(
      Validate::request([
        "test" => ["min_length=7"],
      ])
    );
    $this->assertFalse(
      Validate::request([
        "test" => ["min_length=8"],
      ])
    );
  }

  public function test_max_length_rule(): void
  {
    request()->request->add([
      "test" => "testing",
    ]);
    $this->assertTrue(
      Validate::request([
        "test" => ["max_length=7"],
      ])
    );
    $this->assertFalse(
      Validate::request([
        "test" => ["max_length=6"],
      ])
    );
  }

  public function test_uppercase_rule(): void
  {
    request()->request->add([
      "test" => "Testing",
    ]);
    $this->assertTrue(
      Validate::request([
        "test" => ["uppercase"],
      ])
    );
    request()->request->add([
      "test" => "testing",
    ]);
    $this->assertFalse(
      Validate::request([
        "test" => ["uppercase"],
      ])
    );
  }

  public function test_lowercase_rule(): void
  {
    request()->request->add([
      "test" => "testing",
    ]);
    $this->assertTrue(
      Validate::request([
        "test" => ["lowercase"],
      ])
    );
    request()->request->add([
      "test" => "TESTING",
    ]);
    $this->assertFalse(
      Validate::request([
        "test" => ["lowercase"],
      ])
    );
  }

  public function test_symbol_rule(): void
  {
    request()->request->add([
      "test" => "testing1234!",
    ]);
    $this->assertTrue(
      Validate::request([
        "test" => ["symbol"],
      ])
    );
    request()->request->add([
      "test" => "testing1234",
    ]);
    $this->assertFalse(
      Validate::request([
        "test" => ["symbol"],
      ])
    );
  }

  public function test_regex_rule(): void
  {
    request()->request->add([
      "test" => "testing1234!",
    ]);
    $this->assertTrue(
      Validate::request([
        "test" => ["regex=([a-z]+[0-9]{4}!)"],
      ])
    );
  }
}
