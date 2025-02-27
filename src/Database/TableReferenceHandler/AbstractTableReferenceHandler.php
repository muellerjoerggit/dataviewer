<?php

namespace App\Database\TableReferenceHandler;

use App\Database\BaseQuery\BaseQueryLocator;
use App\Database\QueryBuilder\NullQueryBuilder;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\EntityTypes\NullEntity\NullEntity;
use Doctrine\DBAL\ArrayParameterType;
use App\Database\QueryBuilder\QueryBuilderInterface;

abstract class AbstractTableReferenceHandler implements TableReferenceHandlerInterface {

  public function __construct(
    protected readonly EntityTypeSchemaRegister $schemaRegister,
    protected readonly BaseQueryLocator $baseQueryLocator,
  ) {}

  public function getReferencedTableQuery(TableReferenceDefinitionInterface $tableReferenceConfiguration, EntityInterface $fromEntity, array $options = []): QueryBuilderInterface {
    if(!$tableReferenceConfiguration->isValid()) {
      return NullQueryBuilder::create();
    }

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