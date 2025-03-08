<?php

namespace App\Item;

abstract class DataType {

  public const int INTEGER = 1;
  public const int STRING = 2;
  public const int ENUM = 3;
  public const int BOOL = 4;
  public const int DATE_TIME = 5;
  public const int TIME = 6;
  public const int TABLE = 7;
  public const int FLOAT = 8;
  public const int DATE = 9;
  public const int UNKNOWN = 20;

}