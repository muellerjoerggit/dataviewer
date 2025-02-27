<?php

namespace App\Database\TableReference;

use App\Database\QueryBuilder\DaViQueryBuilder;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('database.table_reference')]
interface TableReferenceHandlerInterface {

  public function getReferencedTableQuery(TableReferenceDefinitionInterface $tableReferenceConfiguration, EntityInterface $fromEntity): QueryBuilderInterface;

  public function joinTable(QueryBuilderInterface $queryBuilder, TableReferenceDefinitionInterface $tableReferenceConfiguration, bool $innerJoin = false): void;

  public function getFromSchema(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchema;

  public function getToSchema(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchema;
}
