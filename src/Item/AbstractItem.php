<?php

namespace App\Item;

use App\DaViEntity\EntityKey;

class AbstractItem implements ItemInterface, ReferenceItemInterface {

  protected mixed $values;

  protected bool $redError = false;
  protected bool $yellowError = false;

  public function __construct(
    protected ItemConfigurationInterface $configuration
  ) {}

  public function getRawValues(): mixed {
    return $this->values ?? null;
  }

  public function countValues(): int {
    return count($this->getValuesAsArray());
  }

  public function setRawValues(mixed $value): ItemInterface {
    $this->values = $value;
    return $this;
  }

  public function iterateValues(): \Generator {
    if(is_scalar($this->getRawValues())) {
      yield 0 => $this->getRawValues();
    } elseif (is_array($this->getRawValues())) {
      foreach ($this->getRawValues() as $key => $value) {
        yield $key => $value;
      }
    } else {
      yield from [];
    }
  }

  public function getValuesAsArray(): array {
    $values = $this->getRawValues();
    if(is_scalar($values)) {
      return [$values];
    } elseif (is_array($values)) {
      return $values;
    } else {
      return [];
    }
  }

  public function getFirstValue() {
    $value = $this->getRawValues();
    if(is_array($value)) {
      $value = reset($value);
    }
    return $value;
  }

  public function getFirstValueAsString(): string {
    $values = $this->getRawValues();
    if(is_array($values)) {
      $ret = $values;
      if(empty($ret)) {
        return '';
      }
      return reset($ret);
    } elseif(is_scalar($values)) {
      return $values;
    } elseif ($values === null) {
      return 'NULL';
    }
    return '';
  }

  public function getValuesAsString(): string {
    $values = $this->getRawValues();

    if(is_scalar($values)) {
      return $values;
    } elseif($this->isValuesNull()) {
      return 'NULL';
    } elseif (is_array($values)) {
      return implode(', ', $this->getValuesAsOneDimensionalArray());
    } else {
      return '';
    }
  }

  public function getValuesAsOneDimensionalArray(): array {
    $values = $this->getValuesAsArray();
    $ret = [];
    array_walk_recursive(
      $values,
      function($value) use (&$ret) {
        if(is_scalar($value)) {
          $ret[] = $value;
        }
      });
    return $ret;
  }

  public function __string(): string {
    return $this->getValuesAsString();
  }

  public function getValuesAsCutOffString($length = 50): string {
    $string = $this->getValuesAsString();
    if(mb_strlen($string) > $length) {
      $length = $length - 4;
      $string = mb_substr($this->getValuesAsString(), 0, $length) . ' ...';
    }
    return $string;
  }

  public function isValuesNull(): bool {
    return $this->getRawValues() === null;
  }

  public function getValues(): mixed {
    if($this->isValuesNull()) {
      return null;
    }
    $itemConfiguration = $this->getConfiguration();
    $cardinality = $itemConfiguration->getCardinality();
    $values = $this->getValuesAsArray();
    if(!$itemConfiguration->isCardinalityMultiple()) {
      $values = reset($values);
      return $this->castValue($values);
    }
    return $values;
  }

  private function castValue(mixed $value) {
    if(is_array($value)) {
      return $value;
    }

    switch($this->getConfiguration()->getDataType()) {
      case ItemInterface::DATA_TYPE_STRING:
        return (string)$value;
      case ItemInterface::DATA_TYPE_INTEGER:
        return (int)$value;
      case ItemInterface::DATA_TYPE_BOOL:
        return (bool)$value;
      case ItemInterface::DATA_TYPE_FLOAT:
        return floatval($value);
      default:
        return $value;
    }
  }

  public function getConfiguration(): ItemConfigurationInterface {
    return $this->configuration;
  }

  public function isRedError(): bool	{
    return $this->redError;
  }

  public function setRedError(bool $redError): ItemInterface {
    $this->redError = $redError;
    return $this;
  }

  public function isYellowError(): bool	{
    return $this->yellowError;
  }

  public function setYellowError(bool $yellowError): ItemInterface {
    $this->yellowError = $yellowError;
    return $this;
  }


  protected array $entityKeys = [];

  public function iterateEntityKeys(): \Generator {
    foreach ($this->entityKeys as $entityKey) {
      yield $entityKey;
    }
  }

  public function getEntityKey(): array | EntityKey {
    if(!$this->hasEntityKeys()) {
      return [];
    }

    if(!$this->getConfiguration()->isCardinalityMultiple()) {
      return reset($this->entityKeys);
    }
    return $this->entityKeys;
  }

  public function countEntityKeys(): int {
    return count($this->entityKeys);
  }

  public function addEntityKey(EntityKey | array $entityKeys): ItemInterface {
    if(is_array($entityKeys)) {
      foreach ($entityKeys as $entityKey) {
        if(!($entityKey instanceof EntityKey)) {
          continue;
        }

        $this->addEntityKey($entityKey);
      }
    } else {
      $this->entityKeys[$entityKeys->getFirstEntityKeyAsString()] = $entityKeys;
    }

    return $this;
  }

  public function hasEntityKeys(): bool {
    return !empty($this->entityKeys);
  }

  public function getFirstEntityKey(): EntityKey {
    if(!$this->hasEntityKeys()) {
      return EntityKey::createNullEntityKey();
    }

    $entityKeys = $this->entityKeys;
    return reset($entityKeys);
  }


}