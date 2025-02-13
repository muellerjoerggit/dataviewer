<?php

namespace App\Item\ItemHandler_Validator\Attribute;

use App\Item\ItemHandler\Attribute\AbstractItemHandlerDefinition;

abstract class AbstractValidatorItemHandlerDefinition extends AbstractItemHandlerDefinition implements ValidatorItemHandlerDefinitionInterface {

  public function __construct(
    string $handlerClass
  ) {
    parent::__construct($handlerClass);
  }

}