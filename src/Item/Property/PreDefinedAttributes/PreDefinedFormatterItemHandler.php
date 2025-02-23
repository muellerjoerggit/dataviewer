<?php

namespace App\Item\Property\PreDefinedAttributes;

use App\Item\ItemHandler_Formatter\Attribute\FormatterItemHandlerDefinition;
use App\Item\ItemHandler_Formatter\DateTimeFormatterItemHandler;

class PreDefinedFormatterItemHandler {

  public static function dateTimeFormatterHandler(): array {
    return [
      new FormatterItemHandlerDefinition(
        DateTimeFormatterItemHandler::class,
      ),
    ];
  }

}