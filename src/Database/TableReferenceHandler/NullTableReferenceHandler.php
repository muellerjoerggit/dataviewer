<?php

namespace App\Database\TableReferenceHandler;

use App\Database\DaViQueryBuilder;
use App\Database\TableReference\TableReferenceConfiguration;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypes\NullEntity\NullEntity;

class NullTableReferenceHandler implements TableReferenceHandlerInterface {

  public function getReferencedTableName(TableReferenceConfiguration $tableReferenceConfiguration): string {
    return '';
  }

  public function getReferencedEntityType(TableReferenceConfiguration $tableReferenceConfiguration): string {
    return NullEntity::ENTITY_TYPE;
  }

  public function addWhereCondition(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration, EntityInterface $fromEntity): void {}

}
