<?php

namespace Nebula\Config;

use GalaxyPDO\DB;
use Nebula\Email\EmailerSMTP;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Nebula\Kernel\Env;

class Container
{
    private array $definitions;
    public function __construct()
    {
        $this->definitions = [
            // Auto config for DB
            DB::class => function () {
                $database_config = new \Nebula\Config\Database();
                $db = new DB(
                    $database_config->getConfig(),
                    $database_config->getOptions()
                );
                $db->connect();
                return $db;
            },
            // Twig environment
            Environment::class => function () {
                $config = new \Nebula\Config\Paths();
                $env = Env::getInstance()->env();
                $views = $config->getViews();
                $loader = new FilesystemLoader($views["paths"]);
                return new Environment($loader, [
                    "cache" => $views["cache"],
                    "auto_reload" => strtolower($env["APP_DEBUG"]) === "true",
                ]);
            },
            EmailerSMTP::class => function () {
                $email = new \Nebula\Config\Email();
                $config = $email->getConfig();
                return new EmailerSMTP($config);
            },
        ];
    }

    public function getDefinitions(): array
    {
        return $this->definitions;
    }
}
