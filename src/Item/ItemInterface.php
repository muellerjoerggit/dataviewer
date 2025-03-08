<?php

namespace App\Item;

use Generator;

interface ItemInterface {

  public const int CARDINALITY_SINGLE = 1;
  public const int CARDINALITY_MULTIPLE = 2;

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

}