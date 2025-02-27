<?php

namespace App\Database\TableReference;

use App\Database\QueryBuilder\DaViQueryBuilder;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;

class TableJoinBuilder {

  public function __construct(
    private readonly TableReferenceHandlerLocator $locator,
    private readonly EntityTypeSchemaRegister $schemaRegister,
    private readonly EntityReferenceItemHandlerLocator $referenceItemHandlerLocator,
  ) {}

  public function joinTable(QueryBuilderInterface $queryBuilder, TableReferenceDefinitionInterface $tableReferenceConfiguration): void {
    $handler = $this->locator->getTableHandlerFromConfiguration($tableReferenceConfiguration);
    $handler->joinTable($queryBuilder, $tableReferenceConfiguration);
  }

  public function joinFromPropertyPath(QueryBuilderInterface $queryBuilder, EntitySchema $schema, string $path): void {
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
        $targetEntityType = $tableReferenceConfig->getToEntityClass();
      } else {
        break;
      }

      $this->joinTable($queryBuilder, $tableReferenceConfig);
      $currentSchema = $this->schemaRegister->getEntityTypeSchema($targetEntityType);
    }
  }

}