# Nebula PHP Framework - Model Documentation

Welcome to the documentation for models in the Nebula PHP Framework. This guide will provide you with a comprehensive understanding of how to define, interact with, and utilize models within your Nebula application.

## Table of Contents

- [Introduction to Models](#introduction-to-models)
- [Model Interface](#model-interface)
- [Creating Model Classes](#creating-model-classes)
- [Working with Models](#working-with-models)
  - [Finding Models](#finding-models)
  - [Searching Models](#searching-models)
  - [Creating Models](#creating-models)
  - [Updating and Deleting Models](#updating-and-deleting-models)
- [Sample Model](#sample-model)

## Introduction to Models

Models are essential components in the Nebula PHP Framework that enable you to interact with your application's database tables. Models encapsulate the data and logic associated with a specific database table, providing an organized and efficient way to handle data operations.

## Models Path

The model files are stored in `/app/Models` directory.

## Model Interface

The `Model` interface defines the contract that all Nebula models must adhere to. It includes methods for finding, searching, saving, updating, refreshing, and deleting models.

```php
namespace Nebula\Interfaces\Model;

interface Model 
{
  public static function find(mixed $id): ?self;
  public static function search(array $where): mixed;
  public function insert(): ?self;
  public function update(): void;
  public function refresh(): void;
  public function delete(): void;
}
```

## Creating Model Classes

To create a model class in Nebula, follow these steps:

1. **Namespace and Extending:**

   Create a new PHP class in the desired namespace and extend the `Model` class from the Nebula framework:

   ```php
   use Nebula\Model\Model;

   class YourModel extends Model
   {
       // Model properties and methods
   }
   ```

2. **Define Table Information:**

   Define the `table_name` and `primary_key` properties in your model class to specify the associated database table and primary key:

   ```php
   class YourModel extends Model
   {
       public string $table_name = "your_table_name";
       public string $primary_key = "your_primary_key";
   }
   ```

3. **Guarded Columns:**

   You can define a `$guarded` property that contains columns that should not be inserted or updated using the model:

   ```php
   class YourModel extends Model
   {
       protected array $guarded = [
           "column1",
           "column2",
           // ...
       ];
   }
   ```

## Working with Models

### Finding Models

You can find a model by its primary key using the `find` method:

```php
$model = YourModel::find($id);
```

### Searching Models

Search for models based on specific conditions using the `search` method:

```php
$models = YourModel::search(["column" => $value]);
```

### Creating Models

To create and insert a new model instance into the database, use the `save` method:

```php
$newModel = new YourModel();
$newModel->property = "value";
$newModel->insert();
```

### Updating and Deleting Models

Updating and deleting models are not implemented in the provided code, but you can extend the `Model` class to implement these operations based on your application's needs.

## Sample Model

Here's a sample model class that extends the `Model` class:

```php
use Nebula\Model\Model;

final class User extends Model
{
    public string $table_name = "users";
    public string $primary_key = "id";

    protected array $guarded = [
        "id",
        // ...
    ];

    public function __construct(protected ?string $id = null)
    {
    }
}
```

## Conclusion

Models play a vital role in managing database interactions and organizing data-related logic in your Nebula PHP application. This documentation provides you with the necessary information to create, use, and interact with models effectively.

For more advanced model techniques and integrations, please consult the <s>official Nebula documentation</s>.

Feel free to reach out if you have any questions or need further assistance!
