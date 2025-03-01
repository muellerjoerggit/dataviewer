<?php

namespace App\EntityServices\OverviewBuilder;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class OverviewBuilderDefinition implements OverviewBuilderDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $overviewBuilderClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getOverviewBuilderClass(): string {
    return $this->overviewBuilderClass;
  }

  public function isValid(): bool {
    return !empty($this->overviewBuilderClass);
  }


}