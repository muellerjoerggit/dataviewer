<?php

namespace App\Database\TableReferenceHandler;

use App\Database\DaViQueryBuilder;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttrInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\EntityTypes\NullEntity\NullEntity;

abstract class AbstractTableReferenceHandler implements TableReferenceHandlerInterface {

  public function __construct(
    protected readonly EntityTypeSchemaRegister $schemaRegister,
  ) {}

  public function joinTable(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration, bool $innerJoin = false, string | null $condition = null): void {
    $toTable = $this->getReferencedTableName($tableReferenceConfiguration);
    $fromTable = $this->getSourceTableName($tableReferenceConfiguration->getFromEntityClass());

    if(empty($toTable) || empty($fromTable)) {
      return;
    }

    if($innerJoin) {
      $queryBuilder->innerJoin($fromTable, $toTable, $toTable, $condition);
    } else {
      $queryBuilder->leftJoin($fromTable, $toTable, $toTable, $condition);
    }
  }

  public function joinTableConditionColumn(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration): void {
    $condition = $this->getWhereConditionColumn($queryBuilder, $tableReferenceConfiguration);
    $this->joinTable($queryBuilder, $tableReferenceConfiguration, true, $condition);
  }

  public function joinTableConditionValue(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration, EntityInterface $fromEntity): void {
    $hasWhere = $this->addWhereConditionValue($queryBuilder, $tableReferenceConfiguration, $fromEntity);

    if(!$hasWhere) {
      return;
    }

    $innerJoin = $tableReferenceConfiguration->hasInnerJoin();
    $condition = $this->getWhereConditionColumn($queryBuilder, $tableReferenceConfiguration);

    $this->joinTable($queryBuilder, $tableReferenceConfiguration, $innerJoin, $condition);
  }

  public function getReferencedTableName(TableReferenceAttrInterface $tableReferenceConfiguration): string {
    $referencedEntityType = $this->getReferencedEntityType($tableReferenceConfiguration);
    $schema = $this->schemaRegister->getSchemaFromEntityClass($referencedEntityType);
    return $schema->getBaseTable();
  }

  public function getReferencedEntityType(TableReferenceAttrInterface $tableReferenceConfiguration): string {
    if($tableReferenceConfiguration->isValid()) {
      return $tableReferenceConfiguration->getToEntityClass();
    }

    return NullEntity::class;
  }

  protected function getSourceTableName(string $entityClass): string {
    $schema = $this->schemaRegister->getSchemaFromEntityClass($entityClass);
    return $schema->getBaseTable();
  }

}