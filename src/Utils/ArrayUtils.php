<?php

namespace App\Utils;

use App\DataCollections\TableData;

class ArrayUtils {

  public static function flattenArrayToScalarValues(array $array): array {
    $ret = [];
    array_walk_recursive(
      $array,
      function($value) use (&$ret) {
        if (is_scalar($value)) {
          $ret[] = $value;
        } elseif ($value instanceof TableData) {
          $ret[] = $value->toString();
        }
      });
    return $ret;
  }

  public static function flattenArray(array $array): array {
    $ret = [];
    array_walk_recursive($array, function($value) use (&$ret) {
      $ret[] = $value;
    });
    return $ret;
  }

}
