# Nebula PHP Framework - Controller Documentation

Welcome to the documentation for controllers in the Nebula PHP Framework. This guide will walk you through the usage and features of controllers, a crucial component for organizing your application's logic and handling requests.

## Table of Contents

- [Introduction to Controllers](#introduction-to-controllers)
- [Creating Controllers](#creating-controllers)
- [Validation](#validation)
- [Sample Controller](#sample-controller)

## Introduction to Controllers

Controllers are responsible for processing user requests, managing application logic, and generating responses. In Nebula, controllers help you separate concerns by encapsulating related actions and behavior in individual classes.

## Controller Path

The controller files are stored in `/app/Controllers` directory.


## Creating Controllers

To create a controller in Nebula, follow these steps:

1. **Namespace and Extending:**

   Create a new PHP class in the desired namespace and extend the `Controller` class from the Nebula framework:

   ```php
   use Nebula\Controller\Controller;

   class YourController extends Controller
   {
       // Controller methods and logic
   }
   ```

2. **Defining Methods:**

   Define methods within your controller to handle different actions. These methods will be triggered when specific routes are accessed:

   ```php
   class YourController extends Controller
   {
       public function index(): string
       {
           // Controller logic for the 'index' action
       }
   }
   ```

## Validation

Nebula provides a convenient method for validating incoming request data within your controller methods. The `validate` method allows you to define validation rules and check the validity of the data:

```php
class YourController extends Controller
{
    public function processForm(): string
    {
        if ($this->validate([
            "field1" => ["required", "numeric"],
            "field2" => ["required", "email"],
        ])) {
            // Valid data, perform actions
        } else {
            // Validation failed, handle errors
            $errors = Validate::$errors;
            // Handle errors, redirect, or display messages
        }
    }
}
```

## Sample Controller

Here's a sample controller using attribute-based routing and validation:

```php
use Nebula\Controller\Controller;
use Nebula\Validation\Validate;
use StellarRouter\{Get, Post, Group};

#[Group(prefix: "/admin")]
class SignInController extends Controller
{
    #[Get("/sign-in", "sign-in.index")]
    public function index(): string
    {
        // Controller logic for displaying the sign-in form
    }

    #[Post("/sign-in", "sign-in.post", ["rate_limit"])]
    public function post(): string
    {
        if ($this->validate([
            "email" => ["required", "email"],
            "password" => ["required"],
        ])) {
            // Valid data, perform authentication and redirection
        } else {
            // Validation failed, handle errors and return to the form
            $errors = Validate::$errors;
            // Display errors and show the sign-in form again
        }
    }
}
```

## Conclusion

Controllers are essential components in Nebula PHP Framework that facilitate the handling of user requests, application logic, and response generation. By following the guidelines in this documentation, you can effectively create controllers, handle validation, and structure your application's behavior.

For more advanced controller techniques and integrations, please consult the <s>official Nebula documentation</s>.

Feel free to reach out if you have any questions or need further assistance!
