<?php

namespace App\Item\Property\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class OptionItemSettingDefinition extends AbstractItemSetting {

  public function __construct(
    public readonly array $options,
  ) {}

  public function isValid(): bool {
    return !empty($this->options);
  }

  public function hasOption($option): bool {
    return isset($this->options[$option]);
  }

  public function getLabel($option): string {
    return $this->options[$option] ?? $option;
  }

}