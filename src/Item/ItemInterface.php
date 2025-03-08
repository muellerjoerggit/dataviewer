<?php

namespace App\Item;

use App\DaViEntity\EntityKey;
use Generator;

interface ItemInterface {

  public function countValues(): int;

  public function getValuesAsArray(): array;

  public function getRawValues(): mixed;

  public function iterateValues(): Generator;

  public function getFirstValue(): mixed;

  public function getFirstValueAsString(): string;

  public function getValuesAsString(): string;

  public function isValuesNull(): bool;

  public function getValuesAsOneDimensionalArray(): array;

  public function getCastValues(): mixed;

  public function isRedError(): bool;

  public function setRedError(bool $redError): ItemInterface;

  public function isYellowError(): bool;

  public function setYellowError(bool $yellowError): ItemInterface;

  public function getValues(): mixed;

  public function iterateEntityKeyCollection(): Generator;

  public function hasEntityKeys(): bool;

  public function getEntityKey(): array|EntityKey;

  public function countEntityKeys(): int;

  public function getFirstEntityKey(): EntityKey;

  public function setRawValues(mixed $value): ItemInterface;

  public function __string(): string;

}