<?php

namespace App\EntityServices\OverviewBuilder;

abstract class ExtEntityOverviewTypes {

  public const int SCALAR = 1;
  public const int REFERENCE = 2;
  public const int HTML = 3;
  public const int JSON = 4;
  public const int VALIDATION = 5;
  public const int ADDITIONAL = 6;

}