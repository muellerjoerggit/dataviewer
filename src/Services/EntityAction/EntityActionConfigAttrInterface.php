<?php

namespace App\Services\EntityAction;

interface EntityActionConfigAttrInterface {

  public function isValid(): bool;

  public function getDescription(): string;

  public function getTitle(): string;

  public function getHandler(): string;

}