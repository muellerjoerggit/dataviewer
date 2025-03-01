<?php

namespace App\EntityServices\OverviewBuilder;

enum ExtEntityOverviewEnum: int {

  case SCALAR = 1;
  case REFERENCE = 2;
  case HTML = 3;
  case JSON = 4;
  case VALIDATION = 5;
  case ADDITIONAL = 6;

}
