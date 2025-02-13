<?php

namespace App\DaViEntity\DataProvider;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class DataProviderDefinition implements DataProviderDefinitionInterface {

  public function __construct(
    public readonly string $dataProviderClass,
  ) {}

  public function getDataProviderClass(): string {
    return $this->dataProviderClass;
  }

  public function isValid(): bool {
    return !empty($this->dataProviderClass);
  }

}