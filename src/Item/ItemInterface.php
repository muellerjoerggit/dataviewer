<?php

namespace App\Item;

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

}