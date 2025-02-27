<?php

namespace App\Database\TableReferenceHandler;


use App\Database\BaseQuery\BaseQueryLocator;
use App\Database\DatabaseLocator;
use App\Database\QueryBuilder\DaViQueryBuilder;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\TableReferenceHandler\Attribute\CommonTableReferenceDefinition;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class CommonTableReferenceHandler extends AbstractTableReferenceHandler {

	public function __construct(
    protected readonly DatabaseLocator $databaseLocator,
    EntityTypeSchemaRegister $schemaRegister,
    BaseQueryLocator $baseQueryLocator,
  ) {
    parent::__construct($schemaRegister, $baseQueryLocator);
  }

  public function getJoinCondition(QueryBuilderInterface $queryBuilder, TableReferenceDefinitionInterface $tableReferenceConfiguration): string | null {
    if(!$tableReferenceConfiguration instanceof CommonTableReferenceDefinition) {
      return null;
    }

    $toEntityType = $tableReferenceConfiguration->getToEntityClass();
    $toColumn = $this->getColumn($toEntityType, $tableReferenceConfiguration->getToPropertyCondition());
    $fromColumn = $this->getColumn($tableReferenceConfiguration->getFromEntityClass(), $tableReferenceConfiguration->getFromPropertyCondition());

    if(empty($fromColumn) || empty($toColumn)) {
      return null;
    }

    return $queryBuilder->expr()->eq($toColumn, $fromColumn);
  }

  protected function getColumn(string $entityClass, string $property): string {
    $schema = $this->schemaRegister->getSchemaFromEntityClass($entityClass);
    return !empty($property) ? $schema->getColumn($property) : '';
  }

}
