<?php

namespace App\Services;

use ReflectionClass;
use ReflectionException;

abstract class AbstractAttributesReader {

  protected function reflectClass(string $classname): ReflectionClass | null {
    try {
      $reflection = new ReflectionClass($classname);
    } catch (ReflectionException $e) {
      return null;
    }
    return $reflection;
  }

}