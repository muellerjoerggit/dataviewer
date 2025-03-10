<?php

namespace App\Database\SqlFilterHandler;

interface DateTimeFilterHandlerInterface {

  public const int FILTER_TYPE_EXACT = 1;

  public const int FILTER_TYPE_BEFORE = 2;

  public const int FILTER_TYPE_AFTER = 3;

  public const int FILTER_TYPE_BETWEEN = 4;

}
