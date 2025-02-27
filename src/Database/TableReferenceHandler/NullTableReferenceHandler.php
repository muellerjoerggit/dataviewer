<?php

namespace App\Database\TableReferenceHandler;

use App\Database\QueryBuilder\DaViQueryBuilder;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\EntityTypes\NullEntity\NullEntity;

class NullTableReferenceHandler implements TableReferenceHandlerInterface {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
  ) {}

  public function joinTable(QueryBuilderInterface $queryBuilder, TableReferenceDefinitionInterface $tableReferenceConfiguration, bool $innerJoin = false): void {}

  public function getReferencedTableQuery(TableReferenceDefinitionInterface $tableReferenceConfiguration, EntityInterface $fromEntity): QueryBuilderInterface {
    // TODO: Implement getReferencedTableQuery() method.
  }

  public function getFromSchema(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchema {
    return $this->entityTypeSchemaRegister->getSchemaFromEntityClass(NullEntity::class);
  }

  public function getToSchema(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchema {
    return $this->entityTypeSchemaRegister->getSchemaFromEntityClass(NullEntity::class);
  }

}
