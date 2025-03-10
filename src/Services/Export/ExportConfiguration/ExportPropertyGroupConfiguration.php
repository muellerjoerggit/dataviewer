<?php

namespace App\Services\Export\ExportConfiguration;

use App\Item\Property\PropertyConfiguration;

class ExportPropertyGroupConfiguration implements ExportGroupConfigurationInterface {

  private string $label;
  private array $options;

  public function __construct(
    private readonly string $key,
    private readonly string $exporterClass,
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

  public function setLabel(string $label): ExportPropertyGroupConfiguration {
    if(!empty($label)) {
      $this->label = $label;
    }
    return $this;
  }

  public function setOptions(array $options): ExportPropertyGroupConfiguration {
    $this->options = $options;
    return $this;
  }

  public function getExporterClass(): string {
    return $this->exporterClass;
  }

}