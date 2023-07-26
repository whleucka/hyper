<?php

declare(strict_types=1);

namespace Nebula\Tests\Container;

use Nebula\Container\Container;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
  public function test_container_binding_interface(): void
  {
    $container = new Container();
    $container->bind(DatabaseInterface::class, MySQLDatabase::class);

    $db1 = $container->get(DatabaseInterface::class);
    $db2 = $container->get(DatabaseInterface::class);

    $this->assertInstanceOf(MySQLDatabase::class, $db1);
    $this->assertInstanceOf(MySQLDatabase::class, $db2);
    $this->assertNotSame($db1, $db2, 'Non-singleton classes should not return the same instance.');
  }

  public function test_singleton_binding(): void
  {
    $container = new Container();
    $container->singleton(DatabaseInterface::class, MySQLDatabase::class);

    $db1 = $container->get(DatabaseInterface::class);
    $db2 = $container->get(DatabaseInterface::class);

    $this->assertInstanceOf(MySQLDatabase::class, $db1);
    $this->assertInstanceOf(MySQLDatabase::class, $db2);
    $this->assertSame($db1, $db2, 'Singleton classes should return the same instance.');
  }

  public function test_unknown_binding_throws_exception(): void
  {
    $this->expectException(\Exception::class);
    $container = new Container();
    $container->get(BogusInterface::class);
  }

  public function test_autowire_binding(): void
  {
    $container = new Container();
    $a = $container->get(A::class);
    $this->assertInstanceOf(A::class, $a);
    $this->assertInstanceOf(B::class, $a->getDependency());
    $c = $container->get(C::class);
    $this->assertInstanceOf(C::class, $c);
    $this->assertInstanceOf(A::class, $c->getDependencyA());
    $this->assertInstanceOf(B::class, $c->getDependencyB());
  }

  public function test_complex_dependency_throws_exception(): void
  {
    $this->expectException(\Exception::class);
    $container = new Container();
    $container->get(D::class);
  }

  public function test_binding_with_closure(): void
  {
    $container = new Container();
    $container->bind(D::class, fn() => new D(['test' => "OK!"]));
    $d = $container->get(D::class);
    $this->assertInstanceOf(D::class, $d);
    $this->assertSame(["test" => "OK!"], $d->getConfig());
  }
}

final class A
{
  public function __construct(private B $b)
  {
  }

  public function getDependency(): mixed
  {
    return $this->b;
  }
}

final class B
{
  public function __construct()
  {
  }
}

final class C
{
  public function __construct(private A $a, private B $b)
  {
  }

  public function getDependencyA(): mixed
  {
    return $this->a;
  }

  public function getDependencyB(): mixed
  {
    return $this->b;
  }
}

final class D
{
  public function __construct(private array $config)
  {
  }
  public function getConfig(): array
  {
    return $this->config;
  }
}

final class MySQLDatabase implements DatabaseInterface
{
  public function connect(): void
  {
  }
}

interface DatabaseInterface
{
  public function connect(): void;
}
