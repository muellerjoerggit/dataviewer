<?php

namespace App\Services;

class UniqueKey {

  private array $uniqueKey = [];

  public function addKeyValue(string $property, int | string $value): void {
    $this->uniqueKey[$property] = $value;
  }

  public function getUniqueKey(): array {
    return $this->uniqueKey;
  }

  public function getKeyPropertiesAsString(): string {
    return implode('+', array_keys($this->uniqueKey));
  }

  public function getKeyProperties(): array {
    return array_keys($this->uniqueKey);
  }

  public function isCompositeKey(): bool {
    return count($this->uniqueKey) > 1;
  }

}