<?php

namespace App\Item\Property\Attribute;

use App\Item\Cardinality;

use App\Services\Version\VersionInformation;
use App\Services\Version\VersionInformationWrapperInterface;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyDefinition extends AbstractPropertyAttribute implements VersionInformationWrapperInterface {

  public function __construct(
    public readonly int $dataType,
    public readonly string $label = '',
    public readonly string $description = '',
    public readonly int $cardinality = Cardinality::SINGLE,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getDataType(): int {
    return $this->dataType;
  }

  public function getLabel(): string {
    return $this->label;
  }

  public function hasDescription(): bool {
    return !empty($this->description);
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function getCardinality(): int {
    return $this->cardinality;
  }

  public function getVersionInformation(): array {
    return [
      VersionInformation::SINCE_VERSION => $this->sinceVersion,
      VersionInformation::UNTIL_VERSION => $this->untilVersion,
    ];
  }

}