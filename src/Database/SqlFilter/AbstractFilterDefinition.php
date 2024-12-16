<?php

namespace App\Database\SqlFilter;

abstract class AbstractFilterDefinition implements SqlFilterDefinitionInterface {

  protected string $title = '';
  protected string $description = '';

  protected array $definitions = [];
  protected mixed $defaultValue = null;

  public function __construct(
    protected readonly string $key,
    protected readonly string $handler
  ) {
  }

  public function setTitle(string $title): SqlFilterDefinitionInterface {
    $this->title = $title;
    return $this;
  }

  public function setDescription(string $description): SqlFilterDefinitionInterface {
    $this->description = $description;
    return $this;
  }

  public function getHandler(): string {
    return $this->handler;
  }

  public function setDefaultValue($defaultValue): SqlFilterDefinitionInterface {
    $this->defaultValue = $defaultValue;
    return $this;
  }

  public function getDefaultValue(mixed $default = null): mixed {
    return $this->defaultValue ?? $default;
  }

  public function hasDefaultValue(): bool {
    return $this->defaultValue !== null;
  }

  public function getKey(): string {
    return $this->key;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function setSetting(string $key, mixed $value): SqlFilterDefinitionInterface {
    $this->definitions[$key] = $value;
    return $this;
  }

  public function setSettings(array $settings): SqlFilterDefinitionInterface {
    $this->definitions = $settings;
    return $this;
  }

  public function getSetting(string $key, $defaultValue = null): mixed {
    return $this->definitions[$key] ?? $defaultValue;
  }

  public function setProperty(string $property): SqlFilterDefinitionInterface {
    $this->definitions[SqlFilterDefinitionInterface::YAML_KEY_PROPERTY] = $property;
    return $this;
  }

  public function getProperty(): string {
    return $this->definitions[SqlFilterDefinitionInterface::YAML_KEY_PROPERTY] ?? '';
  }

}