<?php

namespace App\EntityServices\Repository;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class RepositoryDefinition implements RepositoryDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $repositoryClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getRepositoryClass(): string {
    return $this->repositoryClass;
  }

  public function isValid(): bool {
    return !empty($this->repositoryClass);
  }

}