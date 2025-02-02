<?php

namespace App\Database\TableReferenceHandler;


use App\Database\DatabaseLocator;
use App\Database\DaViQueryBuilder;
use App\Database\TableReferenceHandler\Attribute\CommonTableReferenceAttr;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttrInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class CommonTableReferenceHandler extends AbstractTableReferenceHandler {

	public function __construct(
    protected readonly DatabaseLocator $databaseLocator,
    EntityTypeSchemaRegister $schemaRegister,
  ) {
    parent::__construct($schemaRegister);
  }

  public function addWhereConditionValue(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration, EntityInterface $fromEntity): bool {
    if(!$tableReferenceConfiguration instanceof CommonTableReferenceAttr) {
      return false;
    }

    $toEntityType = $tableReferenceConfiguration->getToEntityClass();
    $toColumn = $this->getColumn($toEntityType, $tableReferenceConfiguration->getToPropertyCondition());

    if(empty($toColumn)) {
      return false;
    }

    $item = $fromEntity->getPropertyItem($tableReferenceConfiguration->getFromPropertyCondition());
    $value = $item->getFirstValue();

    $queryBuilder->where($queryBuilder->expr()->eq($toColumn, ':value'));
    $queryBuilder->setParameter('value', $value, $item->getConfiguration()->getQueryParameterType());

    return true;
  }

  public function getWhereConditionColumn(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration): string | null {
    if(!$tableReferenceConfiguration instanceof CommonTableReferenceAttr) {
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
    $toSchema = $this->schemaRegister->getSchemaFromEntityClass($entityClass);
    return !empty($property) ? $toSchema->getColumn($property) : '';
  }

}
