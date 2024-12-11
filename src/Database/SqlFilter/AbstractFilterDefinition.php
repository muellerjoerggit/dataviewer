<?php

namespace App\Database\SqlFilter;

abstract class AbstractFilterDefinition implements SqlFilterDefinitionInterface {

  protected string $title = '';

  protected string $description = '';

  protected array $definitions = [];

  protected mixed $defaultValue = NULL;

  public function __construct(
    protected readonly string $key,
    protected readonly string $handler
  ) {}

  public function getHandler(): string {
    return $this->handler;
  }

  public function getDefaultValue(mixed $default = NULL): mixed {
    return $this->defaultValue ?? $default;
  }

  public function setDefaultValue($defaultValue): SqlFilterDefinitionInterface {
    $this->defaultValue = $defaultValue;
    return $this;
  }

  public function hasDefaultValue(): bool {
    return $this->defaultValue !== NULL;
  }

  public function getKey(): string {
    return $this->key;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function setTitle(string $title): SqlFilterDefinitionInterface {
    $this->title = $title;
    return $this;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function setDescription(string $description): SqlFilterDefinitionInterface {
    $this->description = $description;
    return $this;
  }

  public function setSetting(string $key, mixed $value): SqlFilterDefinitionInterface {
    $this->definitions[$key] = $value;
    return $this;
  }

  public function setSettings(array $settings): SqlFilterDefinitionInterface {
    $this->definitions = $settings;
    return $this;
  }

  public function getSetting(string $key, $defaultValue = NULL): mixed {
    return $this->definitions[$key] ?? $defaultValue;
  }

  public function getType(): int {
    if ($this instanceof SqlFilterDefinition) {
      return SqlFilterDefinitionInterface::FILTER_TYPE_STANDALONE;
    } elseif ($this instanceof SqlGeneratedFilterDefinition) {
      return SqlFilterDefinitionInterface::FILTER_TYPE_GENERATED;
    }
    return SqlFilterDefinitionInterface::FILTER_TYPE_UNKNOWN;
  }

}