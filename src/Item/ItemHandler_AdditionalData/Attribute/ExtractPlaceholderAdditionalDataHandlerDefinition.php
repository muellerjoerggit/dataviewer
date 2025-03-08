<?php

namespace App\Item\ItemHandler_AdditionalData\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ExtractPlaceholderAdditionalDataHandlerDefinition extends AbstractAdditionalDataHandlerDefinition {

  public const string MODE_HTML = 'html';
  public const string MODE_TEXT = 'text';

  public function __construct(
    string $handlerClass,
    /** @var string[] */
    public readonly array $sourceProperties,
    public readonly string $mode,
  ) {
    parent::__construct($handlerClass);
  }

  public function getSourceProperties(): array {
    return $this->sourceProperties;
  }

  public function getMode(): string {
    return $this->mode;
  }

  public function isValid(): bool {
    return parent::isValid()
      && !empty($this->sourceProperties)
      && in_array($this->mode, [self::MODE_HTML, self::MODE_TEXT]);
  }

}