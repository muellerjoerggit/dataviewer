<?php

namespace App\Database\TableReferenceHandler;

use App\Database\DaViQueryBuilder;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\DaViEntity\EntityInterface;
use App\EntityTypes\NullEntity\NullEntity;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttrInterface;

class NullTableReferenceHandler implements TableReferenceHandlerInterface {

  public function joinTableConditionValue(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration, EntityInterface $fromEntity): void {}

  public function getReferencedTableName(TableReferenceAttrInterface $tableReferenceConfiguration): string {
    return '';
  }

  public function getReferencedEntityType(TableReferenceAttrInterface $tableReferenceConfiguration): string {
    return NullEntity::ENTITY_TYPE;
  }

  public function addWhereConditionValue(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration, EntityInterface $fromEntity): bool {
    return false;
  }

  public function joinTable(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration, bool $innerJoin = false, string | null $condition = null): void {}

  public function joinTableConditionColumn(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration): void {}

}
