<?php

namespace App\DaViEntity\AdditionalData;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class AdditionalDataProvider implements AdditionalDataProviderDefinitionInterface {

  public function __construct(
    public readonly string $additionalDataProviderClass,
  ) {}

  public function getAdditionalDataProviderClass(): string {
    return $this->additionalDataProviderClass;
  }

}