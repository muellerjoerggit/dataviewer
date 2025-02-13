<?php

namespace App\Item\ItemHandler_AdditionalData\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
abstract class AdditionalDataItemHandlerDefinitionAttr extends AbstractAdditionalDataItemHandlerDefinition {

  public function __construct(
    string $handlerClass
  ) {
    parent::__construct($handlerClass);
  }

}