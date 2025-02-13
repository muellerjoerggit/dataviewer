<?php

namespace App\Database\SqlFilterHandler\Attribute;

use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionAttr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class SqlFilterEntityReferenceDefinitionAttr extends SqlFilterDefinitionAttr {

  public function __construct(
    string $filterHandler,
    string $title = 'Filter',
    string $description = '',
    bool $group = true,
    string $groupKey = '',
    public string $targetEntityClass = '',
  ) {
    parent::__construct($filterHandler, $title, $description, $group, $groupKey);
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