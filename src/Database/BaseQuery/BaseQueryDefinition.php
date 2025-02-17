<?php

namespace App\Database\BaseQuery;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class BaseQueryDefinition implements BaseQueryDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $baseQueryClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getBaseQueryClass(): string {
    return $this->baseQueryClass;
  }

  public function isValid(): bool {
    return !empty($this->baseQueryClass);
  }

}