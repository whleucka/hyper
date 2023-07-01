<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class SchemaTest extends TestCase
{
  public function test_schema_create_users_table(): void
  {
    $migration_class = require_once __DIR__ . "/../migrations/1688141260_table_users.php";
    $user_migration = new $migration_class;
    $query = "CREATE TABLE IF NOT EXISTS users (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, uuid CHAR(36) NOT NULL DEFAULT (UUID()), name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password BINARY(96) NOT NULL, remember_token CHAR(32), created_at DATETIME(0) NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP, UNIQUE KEY (email), PRIMARY KEY (id))";
    $this->assertSame(
      $query,
      $user_migration->up()
    );
  }
}
