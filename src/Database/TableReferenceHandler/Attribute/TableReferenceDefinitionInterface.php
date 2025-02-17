<?php

namespace App\Database\TableReferenceHandler\Attribute;

interface TableReferenceDefinitionInterface {

  public function getName(): string;

  public function getHandlerClass(): string;

  public function setExternalName(string $externalName): static;

  public function hasInnerJoin(): bool;

  public function isValid(): bool;

  public function getFromEntityClass(): string;

  public function setFromEntityClass(string $fromEntityClass): static;

  public function getToEntityClass(): string;
}