<?php

namespace App\Item\Property\Attribute;

class AbstractPropertyAttribute {

  protected readonly string $property;

  public function getProperty(): string {
    return $this->property;
  }

  public function setProperty(string $property): static {
    if(isset($this->property)) {
      return $this;
    }

    $this->property = $property;
    return $this;
  }

}