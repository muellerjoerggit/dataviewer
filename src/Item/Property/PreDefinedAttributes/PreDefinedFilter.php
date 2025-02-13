<?php

namespace App\Item\Property\PreDefinedAttributes;

use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionAttr;
use App\Database\SqlFilterHandler\CommonTextFilterHandler;
use App\Database\SqlFilterHandler\DateTimeFilterHandler;
use App\Database\SqlFilterHandler\NullCheckFilterHandler;

class PreDefinedFilter {

  public static function commonTextFilter(): array {
    return [
      new SqlFilterDefinitionAttr(
        CommonTextFilterHandler::class,
        'Textfilter'
      ),
    ];
  }

  public static function dateTimeFilter(): array {
    return [
      new SqlFilterDefinitionAttr(
        DateTimeFilterHandler::class,
        'Datumsfilter'
      ),
    ];
  }

  public static function nullCheckFilter(): array {
    return [
      new SqlFilterDefinitionAttr(
        NullCheckFilterHandler::class,
        'Nullfilter'
      ),
    ];
  }

}