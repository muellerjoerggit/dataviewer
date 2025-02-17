<?php

namespace App\Database\TableReferenceHandler\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CommonTableReferenceDefinition extends TableReferenceDefinition {

  public function __construct(
    string $name,
    string $handlerClass,
    public readonly string $toEntityClass,
    public readonly array $propertyConditions,
  ) {
    parent::__construct($name, $handlerClass);
  }

  public function isValid(): bool {
    if(!parent::isValid()) {
      return false;
    }

    if(empty($this->toEntityClass) || empty($this->propertyConditions)) {
      return false;
    }

    if(empty($this->getToPropertyCondition()) || empty($this->getFromPropertyCondition())) {
      return false;
    }

    return true;
  }

  public function getToEntityClass(): string {
    return $this->toEntityClass;
  }

  public function getFromPropertyCondition(): string {
    return key($this->propertyConditions);
  }

  public function getToPropertyCondition(): string {
    return current($this->propertyConditions);
  }

  public static function create(string $name, string $handlerClass, string $toEntityClass, array $propertyConditions): CommonTableReferenceDefinition {
    return new static($name, $handlerClass, $toEntityClass, $propertyConditions);
  }

}