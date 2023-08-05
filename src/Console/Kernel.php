<?php

namespace Nebula\Console;

use Nebula\Interfaces\Framework\Kernel as ConsoleKernel;
use Nebula\Interfaces\Http\Response;
use Throwable;

class Kernel implements ConsoleKernel
{
    protected Response $response;
    protected string $output = '';
    protected array $option_desc = [
        'h' => 'print help and exit',
        '-help' => 'print help and exit',
    ];

    public function setup(): void
    {
        $this->response = app()->get(Response::class);
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
        logger('timeEnd', 'NebulaConsole');
        exit;
    }

    protected function write(string $content): void
    {
        $this->output .= $content . PHP_EOL;
    }

    protected function run(): void
    {
        $this->write($this->banner());
        $this->write($this->help());
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
        foreach ($this->option_desc as $opt => $desc) {
            $help .= " -{$opt}\t\t\t\t\t{$desc}" . PHP_EOL;
        }
        return $help;
    }
}
