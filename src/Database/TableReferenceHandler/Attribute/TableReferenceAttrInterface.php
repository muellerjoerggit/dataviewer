<?php

namespace App\Database\TableReferenceHandler\Attribute;

interface TableReferenceAttrInterface {

  public function getName(): string;

  public function getHandlerClass(): string;

  public function setExternalName(string $name): static;

  public function hasInnerJoin(): bool;

  public function isValid(): bool;

  public function getFromEntityClass(): string;

  public function setFromEntityClass(string $fromEntityClass): static;

  public function getToEntityClass(): string;
}