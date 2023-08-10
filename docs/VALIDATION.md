# Nebula PHP Framework - Validation Documentation

Validation is a crucial aspect of any web application to ensure that user-submitted data adheres to certain rules and constraints. The Nebula PHP Framework provides a powerful validation system that helps you validate and manage form data effectively. This guide will walk you through how to use the validation system in Nebula, including creating validation rules, applying validation, and handling validation errors.

## Table of Contents

- [Introduction to Validation](#introduction-to-validation)
- [Twig Helper Function](#twig-helper-function)
- [Controller Validation](#controller-validation)
- [Validation Rules](#validation-rules)
- [Custom Validation Rules](#custom-validation-rules)
- [Conclusion](#conclusion)

## Introduction to Validation

Validation in Nebula ensures that the data submitted by users meets the required criteria before it is processed or stored. This helps prevent erroneous or malicious data from affecting your application's functionality and security.

## Twig Helper Function

The Nebula validation system is tightly integrated with Twig templates. The following helper function is provided to make form validation errors available in your Twig templates:

```php
/**
 * Return a twig rendered string
 */
function twig(string $path, array $data = []): string
{
  $twig = app()->get(\Twig\Environment::class);
  $form_errors = Validate::$errors;
  $data['has_form_errors'] = !empty($form_errors);
  $data['form_errors'] = $form_errors;
  return $twig->render($path, $data);
}
```

By using this helper function, you can easily pass validation error information to your Twig templates and display appropriate error messages to users.

## Controller Validation

The validation system is used in Nebula controllers to validate user input. Here's how you can perform validation in a controller class:

1. Extend the `Controller` class from `Nebula\Controller\Controller`.
2. Use the `validate()` method to perform validation on request data.

```php
namespace Nebula\Controller;

use Nebula\Interfaces\Controller\Controller as NebulaController;
use Nebula\Validation\Validate;

class Controller implements NebulaController
{
  // Validation errors
  protected array $errors = [];

  // ...

  protected function validate(array $rules): bool
  {
    $result = Validate::request($rules);
    $this->errors = Validate::$errors;
    return $result;
  }
}
```

## Validation Rules

You can define validation rules using the `validate()` method provided by the controller. Rules are defined as an array, where the key represents the field name and the value represents an array of validation rules.

```php
if ($this->validate([
  "name" => ["required"],
  "email" => ["required", "unique=users", "email"],
  "password" => [
    "required",
    "min_length=8",
    "uppercase=1",
    "lowercase=1",
    "symbol=1"
  ],
  "password_match" => ["Password" => ["required", "match"]]
])) {
  // Validation passed
} else {
  // Validation failed, handle errors
}
```

## Custom Validation Rules

WIP

## Conclusion

The Nebula PHP Framework's validation system allows you to efficiently validate user input, ensuring the accuracy, security, and integrity of your application's data. By understanding how to use validation rules, apply validation in controllers, and handle validation errors, you can create a robust and secure web application.

If you have any questions or need further assistance, please don't hesitate to reach out!
