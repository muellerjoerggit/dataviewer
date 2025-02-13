<?php

namespace App\DaViEntity\ListProvider;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class ListProviderDefinition implements ListProviderDefinitionInterface {

  public function __construct(
    public readonly string $listProviderClass
  ) {}

  public function getListProviderClass(): string {
    return $this->listProviderClass;
  }

  public function isValid(): bool {
    return !empty($this->listProviderClass);
  }

}