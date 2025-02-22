<?php

namespace App\Item\ItemHandler_Validator\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
abstract class ConstraintsValidatorItemHandlerDefinition extends AbstractValidatorItemHandlerDefinition {

  public function __construct(
    string $handlerClass,
    public readonly string $logCode,
    public readonly string $constraintClass,
    public readonly bool $negate = false,
  ) {
    parent::__construct($handlerClass);
  }

  public function getConstraintClass(): string {
    return $this->constraintClass;
  }

  public function isValid(): bool {
    return parent::isValid() && !empty($this->constraintClass);
  }

  public function isNegate(): bool {
    return $this->negate;
  }

  public function getLogCode(): string {
    return $this->logCode;
  }
}