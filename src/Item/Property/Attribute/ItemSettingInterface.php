<?php

namespace App\Item\Property\Attribute;

interface ItemSettingInterface {

  public function getClass(): string;

  public function isValid(): bool;

}