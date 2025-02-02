<?php

namespace App\Services\EntityAction;

abstract class AbstractEntityActionConfigAttr implements EntityActionConfigAttrInterface {

  public function __construct(
    public readonly string $handler,
    public readonly string $title = 'Entity action',
    public readonly string $description = '',
  ) {}

  public function getHandler(): string {
    return $this->handler;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function isValid(): bool {
    return !empty($this->handler);
  }

}