<?php

namespace App\DaViEntity\ColumnBuilder;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ColumnBuilderDefinition implements ColumnBuilderDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $columnBuilderClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getColumnBuilderClass(): string {
    return $this->columnBuilderClass;
  }

  public function isValid(): bool {
    return !empty($this->columnBuilderClass);
  }
}