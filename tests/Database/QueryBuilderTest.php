<?php

declare(strict_types=1);

namespace Nebula\Tests\Database;

use App\Models\User;
use PHPUnit\Framework\TestCase;
use Nebula\Database\QueryBuilder;

final class QueryBuilderTest extends TestCase
{
  public function test_select_query(): void
  {
    $sql = QueryBuilder::select(new User)
    ->columns(["id", "email", "username"])
    ->where(["id" => 1, "username" => "test"])
    ->having(["id" => 2, "username" => "blue"])
    ->groupBy(["id", "username"])
    ->orderBy(["id" => "ASC", "username" => "DESC"])
    ->limit(1)
    ->offset(2)
    ->build();
    $this->assertSame("SELECT id, email, username FROM users WHERE (id = ?) AND (username = ?) HAVING (id = ?) AND (username = ?) GROUP BY id, username ORDER BY id ASC, username DESC LIMIT 1, 2", $sql);
  }
}
