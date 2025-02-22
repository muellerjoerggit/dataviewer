<?php

namespace App\Item\ItemHandler_Validator\Attribute;

use App\Item\ItemHandler_EntityReference\Attribute\AbstractEntityReferenceItemHandlerDefinition;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
abstract class ValidatorItemHandlerDefinition extends AbstractValidatorItemHandlerDefinition {

  public function __construct(
    string $handlerClass,
    public readonly string $logCode,
  ) {
    parent::__construct($handlerClass);
  }

  public function getLogCode(): string {
    return $this->logCode;
  }

}