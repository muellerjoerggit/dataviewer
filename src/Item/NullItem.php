<?php

namespace App\Item;

use Generator;

class NullItem implements ItemInterface {
  protected ItemConfigurationInterface $itemConfiguration;

  public function __construct() {
    $this->itemConfiguration = new ItemConfiguration('NullItem');
  }

  public static function create(): static {
    return new static();
  }

  public function countValues(): int {
    return 0;
  }

  public function getValuesAsArray(): array {
    return [];
  }

  public function getRawValues(): mixed {
    return null;
  }

  public function iterateValues(): Generator {
    yield from [];
  }

  public function getFirstValue(): mixed {
    return null;
  }

  public function getFirstValueAsString(): string {
    return '';
  }

  public function getValuesAsString(): string {
    return '';
  }

  public function isValuesNull(): bool {
    return true;
  }

  public function getValuesAsOneDimensionalArray(): array {
    return [];
  }

  public function getValues(): mixed {
    return null;
  }

  public function getConfiguration(): ItemConfigurationInterface {
    return $this->itemConfiguration;
  }

  public function isRedError(): bool {
    return true;
  }

  public function setRedError(bool $redError): ItemInterface {
    return $this;
  }

  public function isYellowError(): bool {
    return false;
  }

  public function setYellowError(bool $yellowError): ItemInterface {
    return $this;
  }
}