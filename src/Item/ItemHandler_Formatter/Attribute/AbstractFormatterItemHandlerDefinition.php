<?php

namespace App\Item\ItemHandler_Formatter\Attribute;

use App\Item\ItemHandler\Attribute\AbstractItemHandlerDefinition;

abstract class AbstractFormatterItemHandlerDefinition extends AbstractItemHandlerDefinition implements FormatterItemHandlerDefinitionInterface {

  public function __construct(
    string $handlerClass
  ) {
    parent::__construct($handlerClass);
  }

}