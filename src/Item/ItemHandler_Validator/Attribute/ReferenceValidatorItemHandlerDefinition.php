<?php

namespace App\Item\ItemHandler_Validator\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
abstract class ReferenceValidatorItemHandlerDefinition extends AbstractValidatorItemHandlerDefinition {

  public function __construct(
    string $handlerClass,
    public readonly string $commonLogCode = '',
    public readonly bool $mandatory = false,
    public readonly string $mandatoryLogCode = '',
    public readonly bool $notMissing = true,
    public readonly string $notMissingLogCode = '',
    public readonly bool $notAvailable = false,
    public readonly string $notAvailableLogCode = '',
    public readonly bool $notCritical = false,
    public readonly string $notCriticalLogCode = '',
  ) {
    parent::__construct($handlerClass);
  }

  public function isMandatory(): bool {
    return $this->mandatory;
  }

  public function getMandatoryLogCode(): string {
    return $this->mandatoryLogCode ?? $this->commonLogCode;
  }

  public function isNotMissing(): bool {
    return $this->notMissing;
  }

  public function getNotMissingLogCode(): string {
    return $this->notMissingLogCode ?? $this->commonLogCode;
  }

  public function isNotAvailable(): bool {
    return $this->notAvailable;
  }

  public function getNotAvailableLogCode(): string {
    return $this->notAvailableLogCode ?? $this->commonLogCode;
  }

  public function isNotCritical(): bool {
    return $this->notCritical;
  }

  public function getNotCriticalLogCode(): string {
    return $this->notCriticalLogCode ?? $this->commonLogCode;
  }

  public function isValid(): bool {
    return
      parent::isValid()
      && (!$this->mandatory || (!empty($this->mandatoryLogCode) || !empty($this->commonLogCode)))
      && (!$this->notMissing || (!empty($this->notMissingLogCode) || !empty($this->commonLogCode)))
      && (!$this->notAvailable || (!empty($this->notAvailableLogCode) || !empty($this->commonLogCode)))
      && (!$this->notCritical || (!empty($this->notCriticalLogCode) || !empty($this->commonLogCode)))
    ;
  }

}