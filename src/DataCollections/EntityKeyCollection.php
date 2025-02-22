<?php

namespace App\DataCollections;

use App\DaViEntity\EntityKey;
use Generator;

class EntityKeyCollection implements ArrayInterface {

  public const string KEY = 'key';
  public const string RAW_VALUE = 'raw';

  private array $collection = [];

  public function hasEntityKeys(): bool {
    return !empty($this->getAllEntityKeys());
  }

  public function hasValues(): bool {
    return !empty($this->collection);
  }

  public function countEntityKeys(): int {
    return count($this->getAllEntityKeys());
  }

  public function addKey(EntityKey $entityKey, int | string $rawValue): static {
    $this->collection[] = [
      self::KEY => $entityKey,
      self::RAW_VALUE => $rawValue,
    ];
    return $this;
  }

  public function addRawValue(int | string $rawValue): static {
    $this->collection[] = [
      self::KEY => null,
      self::RAW_VALUE => $rawValue,
    ];
    return $this;
  }

  public function getAllEntityKeys(): array {
    $keys = array_column($this->collection, self::KEY);
    return array_filter($keys);
  }

  public function getAllRawValues(): array {
    return array_column($this->collection, self::RAW_VALUE);
  }

  public function toArray(): array {
    return $this->getAllRawValues();
  }

  public function iterateAllEntries(): Generator {
    foreach ($this->collection as $entry) {
      yield $entry;
    }
  }

  public function getFirstEntityKey(): EntityKey {
    $keys = $this->getAllEntityKeys();
    return reset($keys) ?? EntityKey::createNullEntityKey();
  }

}