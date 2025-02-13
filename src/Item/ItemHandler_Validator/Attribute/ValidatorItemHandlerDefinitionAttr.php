<?php

namespace App\Item\ItemHandler_Validator\Attribute;

use App\Item\ItemHandler_EntityReference\Attribute\AbstractEntityReferenceItemHandlerDefinition;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
abstract class ValidatorItemHandlerDefinitionAttr extends AbstractValidatorItemHandlerDefinition {

  public function __construct(
    string $handlerClass
  ) {
    parent::__construct($handlerClass);
  }

}