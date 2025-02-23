<?php

namespace App\EntityServices\EntityLabel;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class LabelCrafterDefinition implements LabelCrafterDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $labelCrafterClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getLabelCrafterClass(): string {
    return $this->labelCrafterClass;
  }

  public function isValid(): bool {
    return !empty($this->labelCrafterClass);
  }
}