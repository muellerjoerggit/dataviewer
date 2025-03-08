<?php

namespace App\EntityServices\AvailabilityVerdict;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class AvailabilityVerdictDefinition implements AvailabilityVerdictDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $availabilityVerdictClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}


  public function isValid(): bool {
    return !empty($this->availabilityVerdictClass);
  }

  public function getAvailabilityVerdictServiceClass(): string {
    return $this->availabilityVerdictClass;
  }

}