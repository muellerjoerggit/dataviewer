<?php

namespace App\Database\SqlFilterHandler\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class SqlFilterEntityReferenceDefinition extends SqlFilterDefinition {

  public function __construct(
    string $filterHandler,
    string $key = '',
    string $title = 'Filter',
    string $description = '',
    bool $group = true,
    string $groupKey = '',
    public string $targetEntityClass = '',
  ) {
    parent::__construct($filterHandler, $key, $title, $description, $group, $groupKey);
  }

  public function hasTargetEntity(): bool {
    return !empty($this->targetEntityClass);
  }

  public function getTargetEntityClass(): string {
    return $this->targetEntityClass;
  }

  public function setTargetEntityClass(string $targetEntityClass): static {
    if(empty($this->targetEntityClass)) {
      $this->targetEntityClass = $targetEntityClass;
    }
    return $this;
  }

}