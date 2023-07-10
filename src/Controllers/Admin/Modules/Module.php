<?php

namespace Nebula\Controllers\Admin\Modules;

class Module
{
  public function __construct(private string $route, private string $title)
  {
  }

  public function getData(): array
  {
    return $this->twigData();
  }

  /**
   * @return array<string,string>
   */
  protected function twigData(): array
  {
    return [
      "title" => $this->getTitle()
    ];
  }

  public function getRoute(): string
  {
    return $this->route;
  }

  public function getTitle(): string
  {
    return $this->title;
  }
}
