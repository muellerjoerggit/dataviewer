<?php

namespace App\DaViEntity\ViewBuilder;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ViewBuilderDefinition implements ViewBuilderDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $viewBuilderClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getViewBuilderClass(): string {
    return $this->viewBuilderClass;
  }

  public function isValid(): bool {
    return !empty($this->viewBuilderClass);
  }


}