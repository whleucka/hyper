<?php

namespace Nebula\Console;

use Nebula\Interfaces\Framework\Kernel as ConsoleKernel;
use Nebula\Interfaces\Http\Response;
use Throwable;

class Kernel implements ConsoleKernel
{
    protected Response $response;
    protected array $paths;
    protected string $output = '';
    protected array $opts = [
        'short' => [
            'h' => 'Print help and exit.',
            's' => 'Start development server.',
            't' => 'Run tests.',
        ],
        'long' => [
            'help' => 'Print help and exit.',
            'migration-list' => 'List all migrations and their status.',
            'migration-run' => 'Run all migrations that have not been run yet.',
            'migration-up:' => 'Run migration up on file. Usage: --migration-up=filename.php',
            'migration-down:' => 'Run migration down on file. Usage: --migration-down=filename.php',
            'migration-fresh' => 'Create new database and run all migrations. Be careful!',
        ],
    ];

    protected function run(): void
    {
        $longopts = array_keys($this->opts['long']);
        $shortopts = implode('', array_keys($this->opts['short']));
        $options = getopt($shortopts, $longopts);
        if (empty($options)) {
            $this->write("Unknown option(s) provided. Use -h or --help for help.");
        }
        foreach ($options as $opt => $value) {
            match ($opt) {
                's' => $this->startServer(),
                't' => $this->runTests(),
                'h', 'help' => $this->displayHelp(),
                'migration-run' => $this->runMigrations(),
                'migration-list' => $this->migrationList(),
                'migration-up' => $this->migration($value, true),
                'migration-down' => $this->migration($value, false),
                'migration-fresh' => $this->migrationFresh(),
                default => $this->displayUnknownOption($value),
            };
        }
    }

    protected function banner(): string
    {
        $banner = <<<EOT
  _   _      _           _       
 | \ | | ___| |__  _   _| | __ _ 
 |  \| |/ _ \ '_ \| | | | |/ _` |
 | |\  |  __/ |_) | |_| | | (_| |
 |_| \_|\___|_.__/ \__,_|_|\__,_|
EOT;
        return $banner;
    }

    protected function help(): string
    {
        $help = <<<EOT
Usage:   nebula [options]

Basic options:
EOT;
        $help .= PHP_EOL;
        foreach ($this->opts as $type => $opts) {
            foreach ($opts as $opt => $desc) {
                $opt = str_replace(':', '', $opt);
                $opt = $type === 'short' ? '-' . $opt : '--' . $opt;
                $spacer = floor(strlen($opt) / 6);
                $offset = 3;
                $spacer = str_repeat("\t", $offset - $spacer);
                $help .= "  {$opt}{$spacer}{$desc}" . PHP_EOL;
            }
        }
        return "\n".$help;
    }

    protected function runTests(): void
    {
        $this->write("Running tests...");
        `./bin/test`;
        $this->terminate();
    }

    protected function startServer(): void
    {
        $this->write("Starting server...");
        `./bin/serve`;
        $this->terminate();
    }

    public function setup(): void
    {
        $this->response = app()->get(Response::class);
        $this->paths = config('paths');
    }

    public function handle(): Response
    {
        try {
            $this->run();
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
        $this->response->setContent($this->output);
        return $this->response;
    }

    public function handleException(Throwable $exception): Response
    {
        $this->response->setContent("Nebula console error!" . PHP_EOL . $exception->getMessage() . PHP_EOL);
        return $this->response;
    }

    public function terminate(): never
    {
        //logger('timeEnd', 'Nebula');
        exit;
    }

    protected function write(string $content): void
    {
        $this->output .= $content . PHP_EOL;
    }

    protected function displayUnknownOption(string $option): void
    {
        $this->write("\nUnknown option: " . $option);
    }

    protected function displayHelp(): void
    {
        $this->write($this->banner());
        $this->write($this->help());
    }
    protected function getMigrationClass(string $file): mixed
    {
        $this->checkMigrationTable();
        if (!file_exists($file)) {
            throw new \Exception("Migration file not found: {$file}");
        }
        return require $file;
    }

    protected function migrationList(): void
    {
        $this->checkMigrationTable();
        $migrations = db()->query("SELECT migration_hash FROM migrations")->fetchAll(\PDO::FETCH_COLUMN);
        $files = $this->getMigrationFiles();
        foreach ($files as $file) {
            $base = basename($file);
            $hash = $this->getFileHash($file);
            $run = in_array($hash, $migrations);
            $this->write(($run ? "[OK]" : "[PENDING]") . " {$base}");
        }
    }

    protected function migration(string $file, bool $up, $skip = false): void
    {
        $base = basename($file);
        $filename = $this->paths['migrations'] . $base;
        $migration = $this->getMigrationClass($filename);
        $query = $up ? $migration->up() : $migration->down();
        $word = $up ? "up" : "down";

        if (!$skip) {
            $input = readline("Run $base migration [$word]? (y/n): ");
            if (strtolower($input) !== 'y') {
                $this->write("Migration cancelled!");
                return;
            }
        }

        if ($up && $this->migrationExists($filename)) {
            $this->write("Migration already exists, skipping: {$file}");
            return;
        }

        $this->write("Running migration $word on {$base}");
        $result = db()->query($query);

        if ($result) {
            if ($up) $this->recordMigration($filename);
            else $this->deleteMigration($filename);
            $this->write("Migration $word successful!");
        } else {
            $this->write("Migration $word failed!");
        }
    }

    protected function checkMigrationTable(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS migrations (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        migration_hash VARCHAR(32) NOT NULL,
        ts TIMESTAMP(0) NOT NULL DEFAULT NOW(), 
        PRIMARY KEY (id),
        UNIQUE (migration_hash))";
        $result = db()->query($query);
        if (!$result) {
            throw new \Exception("Failed to create migrations table!");
        }
    }

    public function getFileHash(string $file): string
    {
        return md5($file);
    }

    protected function migrationFresh(): void
    {
        $input = readline("Are you sure you want to create a new database and run all migrations? (y/n): ");
        if (strtolower($input) !== 'y') {
            $this->write("Migration cancelled!");
            return;
        }
        $this->dropDatabase();
        $this->createDatabase();
        $this->checkMigrationTable();
        $this->runMigrations(true);
    }

    protected function getMigrationFiles(): array|bool
    {
        return glob($this->paths['migrations'] . '*.php');
    }

    protected function runMigrations($skip = false): void
    {
        $this->write("Running migrations...");
        $files = $this->getMigrationFiles();
        foreach ($files as $file) {
            if (!$this->migrationExists($file)) {
                $this->migration($file, true, $skip);
            }
        }
        $this->write("Migrations complete!");
    }

    protected function dropDatabase()
    {
        $db_name = config('database')['name']; 
        db()->query("DROP DATABASE IF EXISTS " . $db_name);
    }

    protected function createDatabase()
    {
        $db_name = config('database')['name']; 
        db()->query("CREATE DATABASE IF NOT EXISTS " . $db_name);
        db()->query("USE " . $db_name);
        $this->write("Database created!");
    }

    protected function migrationExists($file): bool
    {
        $result = db()->select("SELECT * FROM migrations WHERE migration_hash = ?", $this->getFileHash($file));
        return !is_null($result) && $result !== false;
    }

    protected function recordMigration($file): void
    {
        db()->query("INSERT IGNORE INTO migrations SET migration_hash = ?", $this->getFileHash($file));
    }

    protected function deleteMigration($file): void
    {
        db()->query("DELETE FROM migrations WHERE migration_hash = ?", $this->getFileHash($file));
    }

}
