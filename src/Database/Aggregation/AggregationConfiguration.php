<?php

namespace App\Database\Aggregation;

use App\Services\AppNamespaces;

class AggregationConfiguration {

  private string $title;

  private string $description;

  private string $name;

  private string $handler;

  private array $properties;

  private array $definitions = [];

  public function __construct(string $name) {
    $this->name = $name;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function setTitle(string $title): AggregationConfiguration {
    $this->title = $title;
    return $this;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function setDescription(string $description): AggregationConfiguration {
    $this->description = $description;
    return $this;
  }

  public function getProperties(): array {
    return $this->properties ?? [];
  }

  public function setProperties(array $properties): AggregationConfiguration {
    $this->properties = $properties;
    return $this;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setName(string $name): AggregationConfiguration {
    $this->name = $name;
    return $this;
  }

  public function getHandler(): string {
    return AppNamespaces::buildNamespace(AppNamespaces::AGGREGATION_HANDLER, $this->handler);
  }

  public function setHandler(string|array $handler): AggregationConfiguration {
    if (is_array($handler)) {
      reset($handler);
      $handlerConfig = current($handler);
      $handler = key($handler);
      $this->definitions = array_merge($this->definitions, is_array($handlerConfig) ? $handlerConfig : []);
    }

    $this->handler = $handler;
    return $this;
  }

  public function getSetting(string $setting, mixed $default = []): mixed {
    return $this->definitions[$setting] ?? $default;
  }

  public function setSetting($key, array $setting): AggregationConfiguration {
    $this->definitions[$key] = $setting;
    return $this;
  }

}
