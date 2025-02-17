<?php

namespace App\DaViEntity\SimpleSearch;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class SimpleSearchDefinition implements SimpleSearchDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $simpleSearchClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getSimpleSearchClass(): string {
    return $this->simpleSearchClass;
  }

  public function isValid(): bool {
    return !empty($this->simpleSearchClass);
  }

}