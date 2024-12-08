<?php

namespace App\Database\SqlFilter;

class SqlGeneratedFilterDefinition extends AbstractFilterDefinition {

  public static function create(string $hashName, array $filterArray): SqlGeneratedFilterDefinition {
    return new SqlGeneratedFilterDefinition(
      $hashName,
      $filterArray[SqlFilterDefinitionInterface::YAML_KEY_HANDLER],
      SqlFilterDefinitionInterface::FILTER_TYPE_GENERATED
    );
  }

}