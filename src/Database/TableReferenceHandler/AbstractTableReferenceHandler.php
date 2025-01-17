<?php

namespace App\Database\TableReferenceHandler;

use App\Database\DaViQueryBuilder;
use App\Database\TableReference\TableReferenceConfiguration;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\EntityTypes\NullEntity\NullEntity;

abstract class AbstractTableReferenceHandler implements TableReferenceHandlerInterface {

  public function __construct(
    protected readonly EntityTypeSchemaRegister $schemaRegister,
  ) {}

  public function joinTable(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration, bool $innerJoin = false, string | null $condition = null): void {
    $toTable = $this->getReferencedTableName($tableReferenceConfiguration);
    $fromTable = $this->getSourceTableName($tableReferenceConfiguration->getFromEntityType());

    if(empty($toTable) || empty($fromTable)) {
      return;
    }

    if($innerJoin) {
      $queryBuilder->innerJoin($fromTable, $toTable, $toTable, $condition);
    } else {
      $queryBuilder->leftJoin($fromTable, $toTable, $toTable, $condition);
    }
  }

  public function joinTableConditionColumn(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration): void {
    $condition = $this->getWhereConditionColumn($queryBuilder, $tableReferenceConfiguration);
    $this->joinTable($queryBuilder, $tableReferenceConfiguration, true, $condition);
  }

  public function joinTableConditionValue(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration, EntityInterface $fromEntity): void {
    $hasWhere = $this->addWhereConditionValue($queryBuilder, $tableReferenceConfiguration, $fromEntity);

    if(!$hasWhere) {
      return;
    }

    $innerJoin = $tableReferenceConfiguration->getSetting(TableReferenceHandlerInterface::YAML_PARAM_INNER_JOIN, false);
    $condition = $this->getWhereConditionColumn($queryBuilder, $tableReferenceConfiguration);

    $this->joinTable($queryBuilder, $tableReferenceConfiguration, $innerJoin, $condition);
  }

  public function getReferencedTableName(TableReferenceConfiguration $tableReferenceConfiguration): string {
    $referencedEntityType = $this->getReferencedEntityType($tableReferenceConfiguration);
    $schema = $this->schemaRegister->getEntityTypeSchema($referencedEntityType);
    return $schema->getBaseTable();
  }

  public function getReferencedEntityType(TableReferenceConfiguration $tableReferenceConfiguration): string {
    return $tableReferenceConfiguration->getSetting('entityType', NullEntity::ENTITY_TYPE);
  }

  protected function getSourceTableName(string $entityType): string {
    $schema = $this->schemaRegister->getEntityTypeSchema($entityType);
    return $schema->getBaseTable();
  }

}