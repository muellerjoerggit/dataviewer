<?php

namespace App\EntityServices\Refiner;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RefinerDefinition implements RefinerDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $refinerClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getRefinerClass(): string {
    return $this->refinerClass;
  }

  public function isValid(): bool {
    return !empty($this->refinerClass);
  }

}