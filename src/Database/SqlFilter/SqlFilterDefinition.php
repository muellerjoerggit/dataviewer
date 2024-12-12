<?php

namespace App\Database\SqlFilter;

class SqlFilterDefinition extends AbstractFilterDefinition {

  public static function createFromArray(array $filterArray): SqlFilterDefinitionInterface {
		return new SqlFilterDefinition(
      $filterArray[SqlFilterDefinitionInterface::YAML_KEY_NAME],
      $filterArray[SqlFilterDefinitionInterface::YAML_KEY_HANDLER]
    );
	}


}
