<?php

namespace App\DaViEntity\EntityDataProvider;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class EntityDataProvider {

  public const string CLASS_PROPERTY = 'dataProviderClass';

  public function __construct(
    public readonly string $dataProviderClass,
  ) {}

  public function getDataProviderClass(): string {
    return $this->dataProviderClass;
  }

}