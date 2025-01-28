<?php

namespace App\Services\Export\ExportConfiguration;

use App\Item\Property\PropertyConfiguration;

class ExportPropertyConfig {

  private string $label;
  private array $options;
  private int $count = 0;

  public function __construct(
    private readonly string $key,
    private readonly PropertyConfiguration $propertyConfig,
  ) {}

  public function getProperty(): string {
    return $this->propertyConfig->getItemName();
  }

  public function getLabel(): string {
    return empty($this->label) ? $this->propertyConfig->getLabel() : $this->label;
  }

  public function getKey(): string {
    return $this->key;
  }

  public function setLabel(string $label): ExportPropertyConfig {
    if(!empty($label)) {
      $this->label = $label;
    }
    return $this;
  }

  public function setOptions(array $options): ExportPropertyConfig {
    $this->options = $options;
    return $this;
  }

  public function getCount(): int {
    return $this->count;
  }

  public function setCount(int $count): ExportPropertyConfig {
    $this->count = $count;
    return $this;
  }



}