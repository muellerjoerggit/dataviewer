<?php

namespace App\Item\ItemHandler_Validator\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
abstract class ExpressionValidatorItemHandlerDefinition extends AbstractValidatorItemHandlerDefinition {

  public function __construct(
    string $handlerClass,
    public readonly string $expression,
    public readonly string $logCode,
    public readonly bool $negate = false,
  ) {
    parent::__construct($handlerClass);
  }

  public function isValid(): bool {
    return parent::isValid() && !empty($this->expression);
  }

  public function getExpression(): string {
    return $this->expression;
  }

  public function isNegate(): bool {
    return $this->negate;
  }

  public function getLogCode(): string {
    return $this->logCode;
  }
}