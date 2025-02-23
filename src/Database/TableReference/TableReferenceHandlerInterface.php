<?php

namespace App\Database\TableReference;

use App\Database\DaViQueryBuilder;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('database.table_reference')]
interface TableReferenceHandlerInterface {

  public function getReferencedTableQuery(TableReferenceDefinitionInterface $tableReferenceConfiguration, EntityInterface $fromEntity): DaViQueryBuilder;

  public function getToSchema(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchema;
}
