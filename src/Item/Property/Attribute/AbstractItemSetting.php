<?php

namespace App\Item\Property\Attribute;

abstract class AbstractItemSetting implements ItemSettingInterface {

  public function getClass(): string {
    return static::class;
  }

}