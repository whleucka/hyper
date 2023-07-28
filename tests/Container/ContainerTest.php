<?php

declare(strict_types=1);

namespace Nebula\Tests\Container;

use Nebula\Container\Container;
use Nebula\Http\Request as HttpRequest;
use Nebula\Interfaces\Http\Request;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{

  protected function setUp(): void
  {
    $this->container = new Container();
    $this->container->build();
  }

  public function test_container_binding_interface(): void
  {
    $this->container->set(DatabaseInterface::class, \DI\get(MySQLDatabase::class));

    $db1 = $this->container->get(DatabaseInterface::class);
    $db2 = $this->container->get(DatabaseInterface::class);

    $this->assertInstanceOf(MySQLDatabase::class, $db1);
    $this->assertInstanceOf(MySQLDatabase::class, $db2);
  }

  public function test_unknown_binding_throws_exception(): void
  {
    $this->expectException(\Exception::class);
    $this->container->get(BogusInterface::class);
  }

  public function test_autowire_binding(): void
  {
    $a = $this->container->get(A::class);
    $this->assertInstanceOf(A::class, $a);
    $this->assertInstanceOf(B::class, $a->getDependency());
    $c = $this->container->get(C::class);
    $this->assertInstanceOf(C::class, $c);
    $this->assertInstanceOf(A::class, $c->getDependencyA());
    $this->assertInstanceOf(B::class, $c->getDependencyB());
  }

  public function test_binding_with_closure(): void
  {
    $this->container->set(D::class, fn() => new D(['test' => "OK!"]));
    $d = $this->container->get(D::class);
    $this->assertInstanceOf(D::class, $d);
    $this->assertSame(["test" => "OK!"], $d->getConfig());
  }

  public function test_circular_dependency_throws_exception(): void
  {
    $this->expectException(\DI\DependencyException::class);
    $e = $this->container->get(E::class);
    $f = $this->container->get(F::class);
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

  public function request(Request $request): Request
  {
    return $request;
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

final class E
{
  public function __construct(private F $f) {}
}

final class F
{
  public function __construct(private E $e) {}
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
