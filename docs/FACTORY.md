# Nebula PHP Framework - Factory Documentation

Welcome to the Factory documentation for the Nebula PHP Framework. This guide will introduce you to the Factory class, which is designed to assist in creating and managing model instances with default or mock data. We'll cover the purpose of the Factory, how to use it to create model instances, and provide an example of creating a user using the `UserFactory`.

## Table of Contents

- [Introduction to Factory](#introduction-to-factory)
- [Using the Factory Class](#using-the-factory-class)
  - [Making Model Instances](#making-model-instances)
  - [Creating a Single Model Instance](#creating-a-single-model-instance)
- [Example: UserFactory](#example-userfactory)
- [Conclusion](#conclusion)

## Introduction to Factory

The Factory class in the Nebula PHP Framework provides a convenient way to create model instances with default or mock data. This is especially useful for generating test data, seeding databases, or quickly creating instances during development.

## Factory Path

The factory files are stored in `/app/Models/Factories` directory.

## Using the Factory Class

The Factory class provides methods for creating and configuring model instances. Here are the key methods available:

### Making Model Instances

The `make` method allows you to create one or more model instances with optional data. You can specify the number of instances to create and whether to save them to the database. Additionally, you can enable mocking to generate mock data.

```php
use Nebula\Model\Factory;

$factory = new Factory();
$models = $factory->make(data: ['key' => 'value'], n: 3, save: true, mock: false);
```

### Creating a Single Model Instance

The `new` method creates a single model instance with optional data. You can provide custom data or use mock data if needed.

```php
use Nebula\Model\Factory;

$factory = new Factory();
$model = $factory->new(['key' => 'value']);
```

## Example: UserFactory

Let's take a look at how you can create a `UserFactory` to create instances of the `User` model. This example showcases how to create a default user, create a user with custom data, and generate mock user data using the Faker library.

```php
namespace App\Models\Factories;

use Nebula\Model\Factory;
use App\Models\User;
use App\Auth;

class UserFactory extends Factory
{
  protected string $model = User::class;

  public function create(string $name, string $email, string $password): ?User
  {
    $user = app()->get($this->model);
    $user->name = $name;
    $user->email = $email;
    $user->password = Auth::hashPassword($password);
    return $user->save();
  }

  public function default(): array
  {
    return [
      'name' => 'Administrator',
      'email' => 'admin@nebula.dev',
      'password' => Auth::hashPassword("admin"); 
    ];
  }

  public function mock(): array
  {
    $faker = \Faker\Factory::create();
    return [
      'name' => $faker->name,
      'email' => $faker->email,
      'password' => $faker->password
    ];
  }
}
```

## Conclusion

The Factory class in the Nebula PHP Framework simplifies the process of creating and configuring model instances. It provides flexibility in generating default and mock data, making it easier to populate your application with test or seed data. The `UserFactory` example showcases how to use the Factory class to create instances of the `User` model with various data scenarios.

Feel free to leverage the Factory class to streamline your data generation needs and improve the efficiency of your development and testing processes.

For more information on advanced Factory techniques and customization options, consult the <s>official Nebula documentation</s>.

If you have any questions or need further assistance, don't hesitate to reach out!
