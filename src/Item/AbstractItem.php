<?php

namespace App\Item;

use App\DataCollections\ArrayInterface;
use App\DataCollections\EntityKeyCollection;
use App\DaViEntity\EntityKey;
use Generator;

abstract class AbstractItem implements ItemInterface {

  protected mixed $values;

  protected bool $redError = FALSE;

  protected bool $yellowError = FALSE;

  public function countValues(): int {
    return count($this->getValuesAsArray());
  }

  public function getValuesAsArray(): array {
    if (is_scalar($this->values)) {
      return [$this->values];
    } elseif (is_array($this->values)) {
      return $this->values;
    } elseif ($this->values instanceof ArrayInterface) {
      return $this->values->toArray();
    } else {
      return [];
    }
  }

  public function getValues(): mixed {
    return $this->values;
  }

  public function getRawValues(): mixed {
    if($this->values instanceof EntityKeyCollection) {
      return $this->values->getAllRawValues();
    } elseif($this->values instanceof ArrayInterface) {
      return $this->values->toArray();
    }

    return $this->values ?? NULL;
  }

  public function setRawValues(mixed $value): ItemInterface {
    $this->values = $value;
    return $this;
  }

  public function iterateValues(): Generator {
    if (is_scalar($this->getRawValues())) {
      yield 0 => $this->getRawValues();
    } elseif (is_array($this->getRawValues())) {
      foreach ($this->getRawValues() as $key => $value) {
        yield $key => $value;
      }
    } else {
      yield from [];
    }
  }

  public function getFirstValue(): mixed {
    $value = $this->getRawValues();
    if (is_array($value)) {
      $value = reset($value);
    }
    return $value;
  }

  public function getFirstValueAsString(): string {
    $values = $this->getRawValues();
    if (is_array($values)) {
      $ret = $values;
      if (empty($ret)) {
        return '';
      }
      return reset($ret);
    } elseif (is_scalar($values)) {
      return $values;
    } elseif ($values === NULL) {
      return 'NULL';
    }
    return '';
  }

  public function __string(): string {
    return $this->getValuesAsString();
  }

  public function getValuesAsString(): string {
    $values = $this->getRawValues();

    if (is_scalar($values)) {
      return $values;
    } elseif ($this->isValuesNull()) {
      return 'NULL';
    } elseif (is_array($values)) {
      return implode(', ', $this->getValuesAsOneDimensionalArray());
    } else {
      return '';
    }
  }

  public function isValuesNull(): bool {
    return $this->getRawValues() === NULL;
  }

  public function getValuesAsOneDimensionalArray(): array {
    $values = $this->getValuesAsArray();
    $ret = [];
    array_walk_recursive(
      $values,
      function($value) use (&$ret) {
        if (is_scalar($value)) {
          $ret[] = $value;
        }
      });
    return $ret;
  }

  public function getCastValues(): mixed {
    if ($this->isValuesNull()) {
      return NULL;
    }
    $itemConfiguration = $this->getConfiguration();
    $values = $this->getValuesAsArray();
    if (!$itemConfiguration->isCardinalityMultiple()) {
      $values = reset($values);
      return $this->castValue($values);
    }
    return $values;
  }

  abstract public function getConfiguration();

  private function castValue(mixed $value) {
    if (is_array($value)) {
      return $value;
    }

    return match ($this->getConfiguration()->getDataType()) {
      DataType::STRING => (string) $value,
      DataType::INTEGER => (int) $value,
      DataType::BOOL => (bool) $value,
      DataType::FLOAT => floatval($value),
      default => $value,
    };
  }

  public function isRedError(): bool {
    return $this->redError;
  }

  public function setRedError(bool $redError): ItemInterface {
    $this->redError = $redError;
    return $this;
  }

  public function isYellowError(): bool {
    return $this->yellowError;
  }

  public function setYellowError(bool $yellowError): ItemInterface {
    $this->yellowError = $yellowError;
    return $this;
  }

  public function iterateEntityKeyCollection(): Generator {
    if($this->values instanceof EntityKeyCollection) {
      foreach ($this->values->iterateAllEntries() as $entry) {
        yield $entry[EntityKeyCollection::RAW_VALUE] => $entry[EntityKeyCollection::KEY];
      }
    } else {
      yield from [];
    }
  }

  public function getEntityKey(): array | EntityKey {
    $multiple = $this->getConfiguration()->isCardinalityMultiple();
    if (!$this->hasEntityKeys()) {
      $key = EntityKey::createNullEntityKey();
      return $multiple ? [$key] : $key;
    }

    return $multiple ? $this->values->getAllEntityKeys() : $this->values->getFirstEntityKey();
  }

  public function hasEntityKeys(): bool {
    return $this->values instanceof EntityKeyCollection && $this->values->hasEntityKeys();
  }

  public function countEntityKeys(): int {
    return $this->values instanceof EntityKeyCollection ? $this->values->countEntityKeys() : 0;
  }

  public function getFirstEntityKey(): EntityKey {
    if ($this->hasEntityKeys()) {
      return $this->values->getFirstEntityKey();
    }

    return EntityKey::createNullEntityKey();
  }

}