# Nebula PHP Framework - Middleware Documentation

Welcome to the middleware documentation for the Nebula PHP Framework. This guide will walk you through the concept of middleware, how to create middleware classes, register them in your application, and how to integrate them into your Nebula application.

## Table of Contents

- [Introduction to Middleware](#introduction-to-middleware)
- [Creating Middleware](#creating-middleware)
  - [Middleware Class](#middleware-class)
  - [Middleware Interface](#middleware-interface)
  - [Example Logger Middleware](#example-logger-middleware)
- [Registering Middleware](#registering-middleware)
- [Using Middleware](#using-middleware)

## Introduction to Middleware

Middleware is a vital component in web applications that allows you to perform actions before or after the core application logic. Middleware can modify requests, responses, or execute specific tasks. Nebula's middleware functionality provides a flexible way to implement cross-cutting concerns such as authentication, logging, and more.

## Creating Middleware

### Middleware Class

To create middleware in Nebula, follow these steps:

1. **Namespace and Extending:**

   Create a new PHP class in the desired namespace and extend the `Middleware` class from the Nebula framework:

   ```php
   use Nebula\Middleware\Middleware;

   class YourMiddleware extends Middleware
   {
       // Middleware methods and logic
   }
   ```

2. **Handle Method:**

   Define the `handle` method within your middleware class. This method receives a `Request` object and a `Closure` representing the next middleware or core logic in the pipeline:

   ```php
   class YourMiddleware extends Middleware
   {
       public function handle(Request $request, Closure $next): Response
       {
           // Middleware logic before the core logic
           $response = $next($request); // Execute next middleware or core logic
           // Middleware logic after the core logic
           return $response;
       }
   }
   ```

### Middleware Interface

The `Middleware` interface defines the contract that all Nebula middleware must adhere to. It includes the `handle` method signature:

```php
namespace Nebula\Interfaces\Middleware;

use Nebula\Interfaces\Http\{Request, Response};
use Closure;

interface Middleware
{
    public function handle(Request $request, Closure $next): Response;
}
```

### Example Logger Middleware

Here's an example of a logger middleware that logs incoming requests:

```php
namespace Nebula\Middleware\Http;

use Nebula\Interfaces\Http\{Response, Request};
use Nebula\Interfaces\Middleware\Middleware;
use Closure;

class Log implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $this->logRequest($request);

        $response = $next($request);

        return $response;
    }

    private function logRequest(Request $request): void
    {
        $logMessage = sprintf(
            "%s %s %s",
            $request->server("REMOTE_ADDR"),
            $request->getMethod(),
            $request->getUri()
        );
        logger("debug", $logMessage);
    }
}
```

## Registering Middleware

In the Nebula PHP Framework, middleware is registered in the `Kernel` class. Open your `Kernel` class (usually located in the `App\Http` namespace) and add your middleware classes to the `$middleware` array in the order you want them to be executed:

```php
namespace App\Http;

use Nebula\Http\Kernel as HttpKernel;

final class Kernel extends HttpKernel
{
  // Register your application middleware classes
  // Middleware classes are executed in the order 
  // they are defined (top to bottom for request, 
  // bottom to top for response)
  protected array $middleware = [
    \Nebula\Middleware\Http\CSRF::class, 
    \Nebula\Middleware\Http\RateLimit::class, 
    \Nebula\Middleware\Admin\Authentication::class, 
    \Nebula\Middleware\Http\CachedResponse::class, 
    \Nebula\Middleware\Http\JsonResponse::class, 
    \Nebula\Middleware\Http\Log::class, 
  ];
}
```

## Using Middleware

You can use middleware in your application to perform actions before or after core logic. Here's how to use middleware:

1. **Creating a Middleware Stack:**

   Create a middleware stack using the `Middleware` class:

   ```php
   $middlewareStack = new Middleware([$middleware1, $middleware2, ...]);
   ```

2. **Handling Middleware:**

   Use the middleware stack to handle incoming requests:

   ```php
   $response = $middlewareStack->handle($request, function ($request) use ($coreLogic) {
       return $coreLogic->process($request);
   });
   ```

   The `$coreLogic` callback is the core application logic that the middleware stack wraps.

## Conclusion

Middleware in the Nebula PHP Framework allows you to perform actions before or after the core application logic, enhancing the functionality and modularity of your application. This documentation equips you with the knowledge to create, implement, register, and use middleware effectively.

For more advanced middleware techniques and integrations, please consult the <s>official Nebula documentation</s>.

Feel free to reach out if you have any questions or need further assistance!
