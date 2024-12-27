<?php

namespace App\Database\TableReferenceHandler;


use App\Database\DatabaseLocator;
use App\Database\DaViQueryBuilder;
use App\Database\TableReference\TableReferenceConfiguration;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class CommonTableReferenceHandler extends AbstractTableReferenceHandler {

	public function __construct(
    protected readonly DatabaseLocator $databaseLocator,
    EntityTypeSchemaRegister $schemaRegister,
  ) {
    parent::__construct($schemaRegister);
  }

  public function addWhereCondition(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration, EntityInterface $fromEntity): void {
    $conditionProperties = $tableReferenceConfiguration->getNestedSetting(
      [],
      TableReferenceHandlerInterface::YAML_PARAM_CONDITION,
      TableReferenceHandlerInterface::YAML_PARAM_CONDITION_PROPERTIES
    );

    if(is_array($conditionProperties)) {
      $fromProperty = key($conditionProperties);
      $toProperty = current($conditionProperties);
    } else {
      return;
    }

    if(!is_string($fromProperty) && !is_string($toProperty)) {
      return;
    }

    $toEntityType = $tableReferenceConfiguration->getSetting(TableReferenceHandlerInterface::YAML_PARAM_ENTITY_TYPE, '');
    $toSchema = $this->schemaRegister->getEntityTypeSchema($toEntityType);

    $item = $fromEntity->getPropertyItem($fromProperty);
    $value = $item->getFirstValue();
    $toColumn = $toSchema->getColumn($toProperty);

    $queryBuilder->where($queryBuilder->expr()->eq($toColumn, ':value'));
    $queryBuilder->setParameter('value', $value, $item->getConfiguration()->getQueryParameterType());
  }

}
