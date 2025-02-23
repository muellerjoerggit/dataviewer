<?php

namespace App\Item\ItemHandler_AdditionalData\Attribute;

use App\Item\ItemHandler\Attribute\AbstractItemHandlerDefinition;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;

abstract class AbstractAdditionalDataHandlerDefinition extends AbstractItemHandlerDefinition implements AdditionalDataHandlerDefinitionInterface {

  public function __construct(
    string $handlerClass
  ) {
    parent::__construct($handlerClass);
  }

}