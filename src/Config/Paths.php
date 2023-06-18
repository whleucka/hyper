<?php

namespace Nebula\Config;

class Paths
{
    private string $projectRoot = __DIR__ . "/../../";
    private string $documentRoot = __DIR__ . "/../../public";
    private string $controllers = __DIR__ . "/../Controllers";
    private array $views = [
        "paths" => __DIR__ . "/../../views",
        "cache" => __DIR__ . "/../../views/.cache",
    ];

    public function getProjectRoot(): string
    {
        return $this->projectRoot;
    }

    public function getDocumentRoot(): string
    {
        return $this->documentRoot;
    }

    public function getControllers(): string
    {
        return $this->controllers;
    }

    public function getViews(): array
    {
        return $this->views;
    }
}
