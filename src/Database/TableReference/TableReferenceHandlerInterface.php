<?php

namespace App\Database\TableReference;

use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('database.table_reference')]
interface TableReferenceHandlerInterface {

  public function getReferencedTableQuery(TableReferenceDefinitionInterface $tableReferenceConfiguration, EntityInterface $fromEntity): QueryBuilderInterface;

}
