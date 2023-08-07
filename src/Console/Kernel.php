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
        ],
        'long' => [
            'help' => 'Print help and exit.',
            'migration-up:' => 'Run migration up on file. Usage: --migration-up=filename.php',
            'migration-down:' => 'Run migration down on file. Usage: --migration-down=filename.php',
        ],
    ];

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
                'h', 'help' => $this->displayHelp(),
                'migration-up' => $this->migration($value, true),
                'migration-down' => $this->migration($value, false),
                default => $this->displayUnknownOption($value),
            };
        }
    }

    public function handleException(Throwable $exception): Response
    {
        $this->response->setContent("Nebula console error!" . PHP_EOL . $exception->getMessage() . PHP_EOL);
        return $this->response;
    }

    public function terminate(): never
    {
        logger('timeEnd', 'Nebula');
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

    protected function migration(string $file, bool $up): void
    {
        $filename = $this->paths['migrations'] . $file;
        $migration = $this->getMigrationClass($filename);
        $query = $up ? $migration->up() : $migration->down();
        $word = $up ? "up" : "down";

        $input = readline("Are you sure you want to run this migration [$word]? (y/n): ");
        if (strtolower($input) !== 'y') {
            $this->write("Migration cancelled!");
            return;
        }
        if ($up && $this->migrationExists($filename)) {
            $this->write("Migration already exists: {$file}");
            return;
        }

        $this->write("Running migration $word on {$file}");
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
