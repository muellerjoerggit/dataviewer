<?php

namespace App\DaViEntity\AdditionalData;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class AdditionalDataProviderDefinition implements AdditionalDataProviderDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $additionalDataProviderClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getAdditionalDataProviderClass(): string {
    return $this->additionalDataProviderClass;
  }

  public function isValid(): bool {
    return !empty($this->additionalDataProviderClass);
  }

}