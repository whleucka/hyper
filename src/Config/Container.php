<?php

namespace Nebula\Config;

use GalaxyPDO\DB;
use Nebula\Email\EmailerSMTP;
use Nebula\Util\TwigExtension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Nebula\Kernel\Env;

$env = Env::getInstance()->env();
return [
    // Auto config for DB
    DB::class => function () {
        $database_config = config("database");
        $config = array_filter(
            $database_config,
            fn($key) => $key != "options",
            ARRAY_FILTER_USE_KEY
        );
        $options = $database_config["options"];
        $db = new DB($config, $options);
        if ($config["enabled"]) {
            $db->connect();
        }
        return $db;
    },
    // Twig environment
    Environment::class => function () {
        $views = config("paths")["views"];
        $loader = new FilesystemLoader($views["paths"]);
        $twig = new Environment($loader, [
            "cache" => $views["cache"],
            "auto_reload" => app()->isDebug(),
        ]);
        $twig->addExtension(new TwigExtension());
        return $twig;
    },
    // Mailer
    EmailerSMTP::class => function () {
        $config = config("email");
        return new EmailerSMTP($config);
    },
];
