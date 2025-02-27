<?php

namespace App\Database;

use App\Database\Exceptions\NotJoinableException;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_EntityReference\SimpleEntityReferenceJoinInterface;

class TableJoinBuilder {

  public function __construct(
    private readonly EntityTypeSchemaRegister $schemaRegister,
    private readonly EntityReferenceItemHandlerLocator $referenceItemHandlerLocator,
  ) {}

  /**
   * @throws NotJoinableException
   */
  public function joinFromPropertyPath(DaViQueryBuilder $queryBuilder, EntitySchema $schema, string $path): void {
    $pathParts = explode('.', $path);
    $currentSchema = $schema;

    foreach ($pathParts as $property) {
      $propertyConfig = $currentSchema->getProperty($property);
      if($propertyConfig->hasEntityReferenceHandler()) {
        $handler = $this->referenceItemHandlerLocator->getEntityReferenceHandlerFromItem($propertyConfig);

        if(!$handler instanceof SimpleEntityReferenceJoinInterface) {
          $handlerClass = get_class($handler);
          throw new NotJoinableException("Handler $handlerClass does not implement SimpleEntityReferenceJoinInterface");
        }

        $handler->joinTable($queryBuilder, $propertyConfig, $schema);
        [$targetEntityClass, $property] = $handler->getTargetSetting($propertyConfig);
      } else {
        break;
      }

      $currentSchema = $this->schemaRegister->getSchemaFromEntityClass($targetEntityClass);
    }
  }

  public function joinTable(
      DaViQueryBuilder $queryBuilder,
      EntitySchema $fromSchema,
      string $fromProperty,
      EntitySchema $toSchema,
      string $toProperty,
      bool $innerJoin = false
    ): bool {

    $condition = $this->getJoinCondition($queryBuilder, $fromSchema, $fromProperty, $toSchema, $toProperty);
    $fromTable = $fromSchema->getBaseTable();
    $toTable = $toSchema->getBaseTable();

    if(empty($toTable) || empty($fromTable) || !$condition) {
      return false;
    }

    if($innerJoin) {
      $queryBuilder->innerJoin($fromTable, $toTable, $toTable, $condition);
    } else {
      $queryBuilder->leftJoin($fromTable, $toTable, $toTable, $condition);
    }
    return true;
  }

  protected function getJoinCondition(DaViQueryBuilder $queryBuilder, EntitySchema $fromSchema, string $fromProperty, EntitySchema $toSchema, string $toProperty): string | null {

    $fromColumn = $this->getColumn($fromSchema, $fromProperty);
    $toColumn = $this->getColumn($toSchema, $toProperty);

    if(empty($fromColumn) || empty($toColumn)) {
      return null;
    }

    return $queryBuilder->expr()->eq($toColumn, $fromColumn);
  }

  protected function getColumn(EntitySchema $schema, string $property): string {
    return !empty($property) ? $schema->getColumn($property) : '';
  }

}