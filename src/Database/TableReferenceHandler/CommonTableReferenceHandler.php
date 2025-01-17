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

  public function addWhereConditionValue(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration, EntityInterface $fromEntity): bool {
    $config = $this->getConditionConfig($tableReferenceConfiguration);
    $toEntityType = $tableReferenceConfiguration->getSetting(TableReferenceHandlerInterface::YAML_PARAM_ENTITY_TYPE, '');
    $toColumn = $this->getColumn($toEntityType, $config['toProperty']);

    if(empty($config['fromProperty']) || empty($toColumn)) {
      return false;
    }

    $item = $fromEntity->getPropertyItem($config['fromProperty']);
    $value = $item->getFirstValue();

    $queryBuilder->where($queryBuilder->expr()->eq($toColumn, ':value'));
    $queryBuilder->setParameter('value', $value, $item->getConfiguration()->getQueryParameterType());

    return true;
  }

  public function getWhereConditionColumn(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration): string | null {
    $config = $this->getConditionConfig($tableReferenceConfiguration);
    $toEntityType = $tableReferenceConfiguration->getSetting(TableReferenceHandlerInterface::YAML_PARAM_ENTITY_TYPE, '');
    $toColumn = $this->getColumn($toEntityType, $config['toProperty']);
    $fromColumn = $this->getColumn($tableReferenceConfiguration->getFromEntityType(), $config['fromProperty']);

    if(empty($fromColumn) || empty($toColumn)) {
      return null;
    }

    return $queryBuilder->expr()->eq($toColumn, $fromColumn);
  }

  protected function getColumn(string $entityType, string $property): string {
    $toSchema = $this->schemaRegister->getEntityTypeSchema($entityType);
    return !empty($property) ? $toSchema->getColumn($property) : '';
  }

  protected function getConditionConfig(TableReferenceConfiguration $tableReferenceConfiguration): array {
    $conditionProperties = $tableReferenceConfiguration->getNestedSetting(
      [],
      TableReferenceHandlerInterface::YAML_PARAM_CONDITION,
      TableReferenceHandlerInterface::YAML_PARAM_CONDITION_PROPERTIES
    );

    $ret = [
      'fromProperty' => '',
      'toProperty' => '',
    ];

    if(is_array($conditionProperties)) {
      $fromProperty = key($conditionProperties);
      $toProperty = current($conditionProperties);
    } else {
      return $ret;
    }

    if(!is_string($fromProperty) && !is_string($toProperty)) {
      return $ret;
    }

    $ret['fromProperty'] = $fromProperty;
    $ret['toProperty'] = $toProperty;

    return $ret;
  }

}
