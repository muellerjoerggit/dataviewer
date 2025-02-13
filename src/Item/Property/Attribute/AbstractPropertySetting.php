<?php

namespace App\Item\Property\Attribute;

abstract class AbstractPropertySetting implements PropertySettingInterface {

  public function getClass(): string {
    return static::class;
  }

}