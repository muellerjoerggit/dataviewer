<?php

namespace App\Database\TableReference;

use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\Schema\EntitySchema;

/**
 * Table references, where column only represents one reference to another table
 */
interface SimpleTableReferenceHandlerInterface extends TableReferenceHandlerInterface {

  public function getToSchema(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchema;

  public function joinTable(QueryBuilderInterface $queryBuilder, TableReferenceDefinitionInterface $tableReferenceDefinition, EntitySchema $fromSchema, bool $innerJoin = true): void;

}