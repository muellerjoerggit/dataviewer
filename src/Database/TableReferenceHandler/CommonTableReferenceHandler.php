<?php

namespace App\Database\TableReferenceHandler;


use App\Database\BaseQuery\BaseQueryLocator;
use App\Database\DatabaseLocator;
use App\Database\DaViQueryBuilder;
use App\Database\TableJoinBuilder;
use App\Database\TableReference\SimpleTableReferenceHandlerInterface;
use App\Database\TableReferenceHandler\Attribute\CommonTableReferenceDefinition;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class CommonTableReferenceHandler extends AbstractTableReferenceHandler implements SimpleTableReferenceHandlerInterface {

	public function __construct(
    protected readonly TableJoinBuilder $joinBuilder,
    EntityTypeSchemaRegister $schemaRegister,
    BaseQueryLocator $baseQueryLocator,
  ) {
    parent::__construct($schemaRegister, $baseQueryLocator);
  }

  public function getToSchema(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchema {
    return $this->schemaRegister->getSchemaFromEntityClass($tableReferenceConfiguration->getToEntityClass());
  }

  public function joinTable(DaViQueryBuilder $queryBuilder, TableReferenceDefinitionInterface $tableReferenceDefinition, EntitySchema $fromSchema, bool $innerJoin = true): void {
    $toSchema = $this->getToSchema($tableReferenceDefinition);
    $targetProperty = $tableReferenceDefinition->getToPropertyCondition();
    $fromProperty = $tableReferenceDefinition->getFromPropertyCondition();

    $this->joinBuilder->joinTable($queryBuilder, $fromSchema, $fromProperty, $toSchema, $targetProperty, $innerJoin);
  }
}
