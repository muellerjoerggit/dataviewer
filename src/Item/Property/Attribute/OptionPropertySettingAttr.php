<?php

namespace App\Item\Property\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class OptionPropertySettingAttr extends AbstractPropertySetting {

  public function isValid(): bool {
    return true;
  }

}