<?php

namespace App\Database\TableReference;

use App\Database\DaViQueryBuilder;
use App\DaViEntity\EntityTypes\NullEntity\NullEntity;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;

class TableJoinBuilder {

  public function __construct(
    private readonly TableReferenceHandlerLocator $locator,
    private readonly EntityTypeSchemaRegister $schemaRegister,
    private readonly EntityReferenceItemHandlerLocator $referenceItemHandlerLocator,
  ) {}

  public function joinTableConditionColumn(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration): void {
    $handler = $this->locator->getTableHandlerFromConfiguration($tableReferenceConfiguration);
    $handler->joinTableConditionColumn($queryBuilder, $tableReferenceConfiguration);
  }

  public function joinFromPropertyPath(DaViQueryBuilder $queryBuilder, EntitySchema $schema, string $path): void {
    $pathParts = explode('.', $path);
    $currentSchema = $schema;

    foreach ($pathParts as $property) {
      $propertyConfig = $currentSchema->getProperty($property);
      if($propertyConfig->hasEntityReferenceHandler()) {
        $handler = $this->referenceItemHandlerLocator->getEntityReferenceHandlerFromItem($propertyConfig);
        $tableReferenceConfig = $handler->buildTableReferenceConfiguration($propertyConfig, $currentSchema);
        $targetEntityType = $handler->getTargetEntityType($propertyConfig);
      } elseif($propertyConfig->hasTableReference()) {
        $tableReferenceConfig = $propertyConfig->getTableReference();
        $targetEntityType = $tableReferenceConfig->getSetting(TableReferenceHandlerInterface::YAML_PARAM_ENTITY_TYPE, NullEntity::ENTITY_TYPE);
      } else {
        break;
      }

      $this->joinTableConditionColumn($queryBuilder, $tableReferenceConfig);
      $currentSchema = $this->schemaRegister->getEntityTypeSchema($targetEntityType);
    }
  }

}