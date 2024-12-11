<?php

namespace App\Database\SqlFilter;

class SqlFilterDefinition extends AbstractFilterDefinition implements PropertyProviderInterface {

  public static function createFromArray(array $filterArray): SqlFilterDefinitionInterface {
    return new SqlFilterDefinition(
      $filterArray[SqlFilterDefinitionInterface::YAML_KEY_NAME],
      $filterArray[SqlFilterDefinitionInterface::YAML_KEY_HANDLER]
    );
  }

  public function setProperty(string $property): SqlFilterDefinitionInterface {
    $this->definitions[SqlFilterDefinitionInterface::YAML_KEY_PROPERTY] = $property;
    return $this;
  }

  public function getProperty(): string {
    return $this->definitions[SqlFilterDefinitionInterface::YAML_KEY_PROPERTY] ?? '';
  }

}
