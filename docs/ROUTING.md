# Nebula PHP Framework - Routing and Attribute-Based Routing Documentation

Welcome to the comprehensive routing documentation for Nebula PHP Framework. This guide covers traditional routing setup and introduces you to the powerful attribute-based routing feature. Whether you prefer conventional route registration or the convenience of attributes, Nebula has you covered.

## Table of Contents

- [Introduction to Routing](#introduction-to-routing)
- [Traditional Route Registration](#traditional-route-registration)
- [Attribute-Based Routing](#attribute-based-routing)
  - [Defining Routes with Attributes](#defining-routes-with-attributes)
  - [Grouping Routes](#grouping-routes)
  - [Using Route Attributes](#using-route-attributes)

## Introduction to Routing

Routing is essential for directing incoming requests to appropriate controllers or actions based on URLs and HTTP methods. Nebula's routing system provides efficient and organized routing management.

## Traditional Route Registration

In the traditional approach, register routes using the `registerRoute` method in the Nebula Router instance:

```php
use Nebula\Interfaces\Routing\Router;
use StellarRouter\Route;

$router = app()->get(Router::class);
$route = new Route('GET', '/home', 'HomeController', 'index');
$router->registerRoute($route);
```

There is a helper method included for adding routes quickly:

```php
// Using a payload closure
app()->route('GET', '/', function() {
    return "Hello, world!";
}, middleware: ['cached']);

// Using a controller endpoint
app()->route('GET', '/', 'SomeController@index', middleware: ['auth']);
```

## Attribute-Based Routing ⭐

Attribute-based routing simplifies route definition using attributes directly in your controller methods. Here's how it works:

1. Import necessary namespaces:

```php
use StellarRouter\{Get, Group, Post};
```
- Note: HTTP methods supported: (GET, POST, PUT, PATCH, DELETE)


2. Attach attributes to controller methods:

```php
#[Group(prefix: "/admin")]
class SignInController extends Controller
{
    #[Get("/sign-in", "sign-in.index")]
    public function index(): string
    {
        // Controller logic for handling GET request to '/admin/sign-in'
    }

    #[Post("/sign-in", "sign-in.post", ["rate_limit"])]
    public function post(): string
    {
        // Controller logic for handling POST request to '/admin/sign-in'
    }
}
```

### Grouping Routes

Group routes for organization and common configurations:

```php
#[Group(prefix: "/admin", middleware: ["auth"])]
class AdminDashboardController extends Controller
{
    // ...
}
```

### Using Route Attributes

Attach additional information to routes using attributes. In SignInController the example, `["rate_limit"]` applies rate limiting to the `post` method.

## Conclusion

Nebula PHP Framework offers both traditional and attribute-based routing options. You can choose the method that suits your project's requirements and coding style. By leveraging Nebula's routing capabilities, you can create well-structured and efficient web applications.

For advanced routing options and techniques, consult the <s>official Nebula documentation</s>.

Feel free to reach out if you have questions or need assistance!
