<?php

namespace App\Item\ItemHandler_AdditionalData\Attribute;

use App\Item\ItemHandler\Attribute\AbstractItemHandlerDefinition;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;

abstract class AbstractAdditionalDataItemHandlerDefinition extends AbstractItemHandlerDefinition implements AdditionalDataItemHandlerDefinitionInterface {

  public function __construct(
    string $handlerClass
  ) {
    parent::__construct($handlerClass);
  }

}