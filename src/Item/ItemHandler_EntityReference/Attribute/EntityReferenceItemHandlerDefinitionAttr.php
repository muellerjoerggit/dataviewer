<?php

namespace App\Item\ItemHandler_EntityReference\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
abstract class EntityReferenceItemHandlerDefinitionAttr extends AbstractEntityReferenceItemHandlerDefinition {

  public function __construct(
    string $handlerClass
  ) {
    parent::__construct($handlerClass);
  }

}