<?php

namespace App\EntityServices\DataProvider;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class DataProviderDefinition implements DataProviderDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $dataProviderClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getDataProviderClass(): string {
    return $this->dataProviderClass;
  }

  public function isValid(): bool {
    return !empty($this->dataProviderClass);
  }

}