<?php

namespace App\DaViEntity\Creator;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CreatorDefinition implements CreatorDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $creatorClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getCreatorClass(): string {
    return $this->creatorClass;
  }

  public function isValid(): bool {
    return !empty($this->creatorClass);
  }

}