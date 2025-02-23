<?php

namespace App\EntityServices\AggregatedData;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class SqlAggregatedDataProviderDefinition implements AggregatedDataProviderDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $aggregatedDataProviderClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getAggregatedDataProviderClass(): string {
    return $this->aggregatedDataProviderClass;
  }

  public function isValid(): bool {
    return !empty($this->aggregatedDataProviderClass);
  }

}