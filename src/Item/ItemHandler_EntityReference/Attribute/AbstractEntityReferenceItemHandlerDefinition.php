<?php

namespace App\Item\ItemHandler_EntityReference\Attribute;

use App\Item\ItemHandler\Attribute\AbstractItemHandlerDefinition;

abstract class AbstractEntityReferenceItemHandlerDefinition extends AbstractItemHandlerDefinition implements EntityReferenceItemHandlerDefinitionInterface {

  public function __construct(
    string $handlerClass
  ) {
    parent::__construct($handlerClass);
  }

}