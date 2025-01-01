<?php

namespace App\Database\TableReferenceHandler;

use App\Database\DaViQueryBuilder;
use App\Database\TableReference\TableReferenceConfiguration;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypes\NullEntity\NullEntity;

class NullTableReferenceHandler implements TableReferenceHandlerInterface {

  public function joinTableConditionValue(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration, EntityInterface $fromEntity): void {}

  public function getReferencedTableName(TableReferenceConfiguration $tableReferenceConfiguration): string {
    return '';
  }

  public function getReferencedEntityType(TableReferenceConfiguration $tableReferenceConfiguration): string {
    return NullEntity::ENTITY_TYPE;
  }

  public function addWhereConditionValue(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration, EntityInterface $fromEntity): bool {
    return false;
  }

  public function joinTable(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration): void {}

}
