# Nebula PHP Framework - Console (CLI) Documentation

Welcome to the console (CLI) documentation for the Nebula PHP Framework. This guide will introduce you to the Nebula console capabilities, how to use the `Kernel` class to execute various commands, and provide insights into some of the available commands.

## Table of Contents

- [Introduction to Console (CLI)](#introduction-to-console-cli)
- [Using the Console Kernel](#using-the-console-kernel)
  - [Kernel Class](#kernel-class)
  - [Basic Commands](#basic-commands)
  - [Running Tests](#running-tests)
  - [Starting the Development Server](#starting-the-development-server)
  - [Managing Migrations](#managing-migrations)
- [Conclusion](#conclusion)

## Introduction to Console (CLI)

The Nebula PHP Framework includes a powerful console (CLI) functionality that allows you to execute various tasks, such as running tests, starting the development server, managing migrations, and more. This feature enables you to manage your Nebula application efficiently from the command line interface.

## Using the Console Kernel

### Kernel Class

The `Kernel` class is the entry point for running console commands in the Nebula framework. It contains methods to handle different commands provided as CLI options. Below is a brief overview of the methods and their corresponding commands:

### Basic Commands

**Note**: There is a symlink to nebula console in the application root. This file points to to `/bin/nebula`

The Nebula CLI supports a few basic commands that can be executed using short or long options. The basic commands include:

- `-h` or `--help`: Print help and exit.
- `-s`: Start the development server.
- `-t`: Run tests.

*Additional commands coming soon*

To run a basic command, use the following syntax:

```bash
./nebula [command]
```

### Running Tests

To run tests for your Nebula application, use the following command:

```bash
./nebula -t
```

This command will execute the tests using the `./bin/test` script.

### Starting the Development Server

To start the development server for your Nebula application, use the following command:

```bash
./nebula -s
```

This command will execute the development server using the `./bin/serve` script.

### Managing Migrations

Nebula CLI allows you to manage database migrations efficiently. The available migration commands are as follows:

- `--migration-list`: List all migrations and their status.
- `--migration-run`: Run all migrations that have not been run yet.
- `--migration-up=[filename.php]`: Run migration up on the specified file.
- `--migration-down=[filename.php]`: Run migration down on the specified file.
- `--migration-fresh`: Create a new database and run all migrations. Caution: this action is irreversible!

To use migration commands, use the following syntax:

```bash
./nebula [migration-command]
```

## Conclusion

The Nebula PHP Framework's console (CLI) functionality provides a convenient and efficient way to manage your application from the command line interface. Whether you're running tests, starting the development server, or managing migrations, the Nebula CLI offers a seamless experience for executing these tasks.

For more advanced CLI commands, customizations, and integrations, please consult the <s>official Nebula documentation</s>.

Feel free to reach out if you have any questions or need further assistance!
