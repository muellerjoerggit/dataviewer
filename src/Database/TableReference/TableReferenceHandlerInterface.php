<?php

namespace App\Database\TableReference;

use App\Database\DaViQueryBuilder;
use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('database.table_reference')]
interface TableReferenceHandlerInterface {

  public const string YAML_PARAM_INNER_JOIN = 'innerJoin';
  public const string YAML_PARAM_ENTITY_TYPE = 'entityType';
  public const string YAML_PARAM_CONDITION = 'condition';
  public const string YAML_PARAM_CONDITION_PROPERTIES = 'properties';

  public function getReferencedTableName(TableReferenceConfiguration $tableReferenceConfiguration): string;

  public function getReferencedEntityType(TableReferenceConfiguration $tableReferenceConfiguration): string;

  public function addWhereCondition(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration, EntityInterface $fromEntity): void;
}
