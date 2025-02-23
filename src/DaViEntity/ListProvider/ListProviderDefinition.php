<?php

namespace App\DaViEntity\ListProvider;

 use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ListProviderDefinition implements ListProviderDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $listProviderClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getListProviderClass(): string {
    return $this->listProviderClass;
  }

  public function isValid(): bool {
    return !empty($this->listProviderClass);
  }

}