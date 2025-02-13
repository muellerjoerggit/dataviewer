<?php

namespace App\Item\ItemHandler\Attribute;

abstract class AbstractItemHandlerDefinition {

  public function __construct(
    public readonly string $handlerClass
  ) {}

  public function getHandlerClass(): string {
    return $this->handlerClass;
  }

  public function isValid(): bool {
    return !empty($this->handlerClass);
  }

}