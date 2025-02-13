<?php

namespace App\Item\Property\Attribute;

use Attribute;
use Generator;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyPreDefinedAttr {

  /**
   * @param array $preDefinedCallbacks
   */
  public function __construct(
    public array $preDefinedCallbacks,
  ) {}

  public function iteratePreDefinedAttributes(): Generator {
    foreach ($this->preDefinedCallbacks as $callback) {
      if(!is_callable($callback)) {
        continue;
      }
      $attrArray = call_user_func($callback);
      if(!is_array($attrArray)) {
        continue;
      }
      foreach ($attrArray as $attr) {
        yield $attr;
      }
    }
  }
}