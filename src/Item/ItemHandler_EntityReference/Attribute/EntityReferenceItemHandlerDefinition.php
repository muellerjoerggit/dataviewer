<?php

namespace App\Item\ItemHandler_EntityReference\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EntityReferenceItemHandlerDefinition extends AbstractEntityReferenceItemHandlerDefinition implements EntityReferenceItemHandlerDefinitionInterface {

  public function __construct(
    string $handlerClass,
    public readonly string $targetEntity,
    public readonly string $targetProperty,
  ) {
    parent::__construct($handlerClass);
  }

  public function getTargetProperty(): string {
    return $this->targetProperty;
  }

  public function getTargetEntity(): string {
    return $this->targetEntity;
  }
}