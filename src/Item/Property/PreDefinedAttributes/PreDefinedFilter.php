<?php

namespace App\Item\Property\PreDefinedAttributes;

use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinition;
use App\Database\SqlFilterHandler\CommonTextFilterHandler;
use App\Database\SqlFilterHandler\DateTimeFilterHandler;
use App\Database\SqlFilterHandler\NullCheckFilterHandler;

class PreDefinedFilter {

  public static function commonTextFilter(): array {
    return [
      new SqlFilterDefinition(
        CommonTextFilterHandler::class,
        '',
        'Textfilter'
      ),
    ];
  }

  public static function dateTimeFilter(): array {
    return [
      new SqlFilterDefinition(
        DateTimeFilterHandler::class,
        '',
        'Datumsfilter'
      ),
    ];
  }

  public static function nullCheckFilter(): array {
    return [
      new SqlFilterDefinition(
        NullCheckFilterHandler::class,
        '',
        'Nullfilter'
      ),
    ];
  }

}