<?php

namespace App\Database\TableReferenceHandler;

use App\Database\DaViQueryBuilder;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\EntityTypes\NullEntity\NullEntity;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;

class NullTableReferenceHandler implements TableReferenceHandlerInterface {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
  ) {}

  public function joinTable(DaViQueryBuilder $queryBuilder, TableReferenceDefinitionInterface $tableReferenceConfiguration, bool $innerJoin = false): void {}

  public function getReferencedTableQuery(TableReferenceDefinitionInterface $tableReferenceConfiguration, EntityInterface $fromEntity): DaViQueryBuilder {
    // TODO: Implement getReferencedTableQuery() method.
  }

  public function getFromSchema(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchema {
    return $this->entityTypeSchemaRegister->getSchemaFromEntityClass(NullEntity::class);
  }

  public function getToSchema(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchema {
    return $this->entityTypeSchemaRegister->getSchemaFromEntityClass(NullEntity::class);
  }

}
