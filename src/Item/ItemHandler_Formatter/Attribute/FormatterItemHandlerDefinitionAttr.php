<?php

namespace App\Item\ItemHandler_Formatter\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
abstract class FormatterItemHandlerDefinitionAttr extends AbstractFormatterItemHandlerDefinition {

  public function __construct(
    string $handlerClass
  ) {
    parent::__construct($handlerClass);
  }

}