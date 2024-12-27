<?php

namespace App\Database\TableReferenceHandler;

use App\Database\TableReference\TableReferenceConfiguration;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\DaViEntity\EntityTypes\NullEntity\NullEntity;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

abstract class AbstractTableReferenceHandler implements TableReferenceHandlerInterface {

  public function __construct(
    protected readonly EntityTypeSchemaRegister $schemaRegister,
  ) {}

  public function getReferencedTableName(TableReferenceConfiguration $tableReferenceConfiguration): string {
    $referencedEntityType = $this->getReferencedEntityType($tableReferenceConfiguration);
    $schema = $this->schemaRegister->getEntityTypeSchema($referencedEntityType);
    return $schema->getBaseTable();
  }

  public function getReferencedEntityType(TableReferenceConfiguration $tableReferenceConfiguration): string {
    return $tableReferenceConfiguration->getSetting('entityType', NullEntity::ENTITY_TYPE);
  }

}