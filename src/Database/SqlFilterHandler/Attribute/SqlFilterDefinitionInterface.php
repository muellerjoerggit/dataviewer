<?php

namespace App\Database\SqlFilterHandler\Attribute;

interface SqlFilterDefinitionInterface {

  public function getProperty(): string;

  public function setProperty(string $property): static;

  public function getFilterHandler(): string;

  public function getTitle(): string;

  public function getDescription(): string;

  public function isGroup(): bool;

  public function isValid(): bool;

  public function hasGroupKey(): bool;

  public function getGroupKey(): string;

  public function getKey(): string;

}