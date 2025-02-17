<?php

namespace App\Database\TableReferenceHandler\Attribute;

use App\Database\TableReferenceHandler\NullTableReferenceHandler;
use Attribute;

abstract class TableReferenceDefinition implements TableReferenceDefinitionInterface {

  protected readonly string $externalName;
  protected readonly string $fromEntityClass;

  public function __construct(
    public readonly string $name,
    public readonly string $handlerClass,
    public readonly bool $innerJoin = false,
  ) {}

  public function getName(): string {
    return $this->name;
  }

  public function getHandlerClass(): string {
    return $this->handlerClass;
  }

  public function setExternalName(string $externalName): static {
    $this->externalName = $externalName;
    return $this;
  }

  public function getExternalName(): string {
    return $this->externalName;
  }

  public function isValid(): bool {
    return !empty($this->externalName) && !empty($this->handlerClass) && !empty($this->fromEntityClass);
  }

  public static function createNullTableReference(string $name, string $externalName): static {
    $attr = new static($name, NullTableReferenceHandler::class);
    $attr->setExternalName($externalName);
    return $attr;
  }

  public function hasInnerJoin(): bool {
    return $this->innerJoin;
  }

  public function setFromEntityClass(string $fromEntityClass): static {
    $this->fromEntityClass = $fromEntityClass;
    return $this;
  }

  public function getFromEntityClass(): string {
    return $this->fromEntityClass;
  }

}