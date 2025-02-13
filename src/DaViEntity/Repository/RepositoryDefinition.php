<?php

namespace App\DaViEntity\Repository;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class RepositoryDefinition implements RepositoryDefinitionInterface {

  public function __construct(
    public readonly string $repositoryClass,
  ) {}

  public function getRepositoryClass(): string {
    return $this->repositoryClass;
  }

  public function isValid(): bool {
    return !empty($this->repositoryClass);
  }

}