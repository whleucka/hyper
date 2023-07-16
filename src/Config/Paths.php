<?php

namespace Nebula\Config;

return [
    "project_root" => __DIR__ . "/../../",
    "document_root" => __DIR__ . "/../../public",
    "controllers" => __DIR__ . "/../Controllers",
    "modules" => __DIR__ . "/../Admin/Modules",
    "views" => [
        "paths" => __DIR__ . "/../../views",
        "cache" => __DIR__ . "/../../views/.cache",
    ],
];
