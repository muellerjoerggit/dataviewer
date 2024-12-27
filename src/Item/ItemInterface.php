<?php

namespace App\Item;

use Generator;

interface ItemInterface {

  public const int DATA_TYPE_INTEGER = 1;
  public const int DATA_TYPE_STRING = 2;
  public const int DATA_TYPE_ENUM = 3;
  public const int DATA_TYPE_BOOL = 4;
  public const int DATA_TYPE_DATE_TIME = 5;
  public const int DATA_TYPE_TIME = 6;
  public const int DATA_TYPE_TABLE = 7;
  public const int DATA_TYPE_FLOAT = 8;
  public const int DATA_TYPE_UNKNOWN = 20;

  public function countValues(): int;

  public function getValuesAsArray(): array;

  public function getRawValues(): mixed;

  public function iterateValues(): Generator;

  public function getFirstValue(): mixed;

  public function getFirstValueAsString(): string;

  public function getValuesAsString(): string;

  public function isValuesNull(): bool;

  public function getValuesAsOneDimensionalArray(): array;

  public function getValues(): mixed;

  public function getConfiguration(): ItemConfigurationInterface;

  public function isRedError(): bool;

  public function setRedError(bool $redError): ItemInterface;

  public function isYellowError(): bool;

  public function setYellowError(bool $yellowError): ItemInterface;


}