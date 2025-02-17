<?php

namespace App\Database\TableReferenceHandler;

use App\Database\BaseQuery\BaseQueryLocator;
use App\Database\DaViQueryBuilder;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\EntityTypes\NullEntity\NullEntity;
use Doctrine\DBAL\ArrayParameterType;

abstract class AbstractTableReferenceHandler implements TableReferenceHandlerInterface {

  public function __construct(
    protected readonly EntityTypeSchemaRegister $schemaRegister,
    protected readonly BaseQueryLocator $baseQueryLocator,
  ) {}

  public function joinTable(DaViQueryBuilder $queryBuilder, TableReferenceDefinitionInterface $tableReferenceConfiguration, bool $innerJoin = false): void {
    $toSchema = $this->getToSchema($tableReferenceConfiguration);
    $fromSchema = $this->getFromSchema($tableReferenceConfiguration);

    $condition = $this->getJoinCondition($queryBuilder, $tableReferenceConfiguration);

    $toTable = $toSchema->getBaseTable();
    $fromTable = $fromSchema->getBaseTable();

    if(empty($toTable) || empty($fromTable) || !$condition) {
      return;
    }

    if($innerJoin) {
      $queryBuilder->innerJoin($fromTable, $toTable, $toTable, $condition);
    } else {
      $queryBuilder->leftJoin($fromTable, $toTable, $toTable, $condition);
    }
  }

  abstract public function getJoinCondition(DaViQueryBuilder $queryBuilder, TableReferenceDefinitionInterface $tableReferenceConfiguration): string | null;

  public function getFromSchema(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchema {
    return $this->schemaRegister->getSchemaFromEntityClass($tableReferenceConfiguration->getFromEntityClass());
  }

  public function getToSchema(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchema {
    return $this->schemaRegister->getSchemaFromEntityClass($tableReferenceConfiguration->getToEntityClass());
  }

  public function getReferencedTableQuery(TableReferenceDefinitionInterface $tableReferenceConfiguration, EntityInterface $fromEntity, array $options = []): DaViQueryBuilder {
    // ToDo: implement NullQueryBuilder
    $referencedEntityClass = $this->getReferencedEntityClass($tableReferenceConfiguration);
    $baseQuery = $this->baseQueryLocator->getBaseQueryFromEntityClass($referencedEntityClass, $fromEntity->getClient());
    $toSchema = $this->schemaRegister->getSchemaFromEntityClass($referencedEntityClass);

    $queryBuilder = $baseQuery->buildQueryFromSchema($referencedEntityClass, $fromEntity->getClient(), $options);
    $property = $tableReferenceConfiguration->getToPropertyCondition();
    $column = $toSchema->getColumn($property);
    $propertyItem = $fromEntity->getPropertyItem($property);

    $queryBuilder->andWhere(
      $queryBuilder->expr()->in($column, ':table_reference_values')
    );
    $queryBuilder->setParameter('table_reference_values', $propertyItem->getValuesAsArray(), ArrayParameterType::INTEGER);

    return $queryBuilder;
  }

  protected function getReferencedEntityClass(TableReferenceDefinitionInterface $tableReferenceConfiguration): string {
    if($tableReferenceConfiguration->isValid()) {
      return $tableReferenceConfiguration->getToEntityClass();
    }

    return NullEntity::class;
  }

}