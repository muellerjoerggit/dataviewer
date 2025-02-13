<?php

namespace App\Item\Property\Attribute;

interface PropertySettingInterface {

  public function getClass(): string;

  public function isValid(): bool;

}