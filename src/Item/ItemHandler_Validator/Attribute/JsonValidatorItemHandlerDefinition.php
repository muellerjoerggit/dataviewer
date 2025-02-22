<?php

namespace App\Item\ItemHandler_Validator\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
abstract class JsonValidatorItemHandlerDefinition extends AbstractValidatorItemHandlerDefinition {

  public function __construct(
    string $handlerClass,
    public readonly string $logCode,
    public readonly bool $jsonObject = true,
    public readonly bool $jsonMandatory = true,
  ) {
    parent::__construct($handlerClass);
  }

  public function getLogCode(): string {
    return $this->logCode;
  }

  public function isJsonObject(): bool {
    return $this->jsonObject;
  }

  public function isJsonMandatory(): bool {
    return $this->jsonMandatory;
  }

}