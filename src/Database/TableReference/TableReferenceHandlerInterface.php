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

  public function joinTable(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration, bool $innerJoin = false, string | null $condition = null): void;

  public function joinTableConditionValue(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration, EntityInterface $fromEntity): void;

  public function getReferencedTableName(TableReferenceConfiguration $tableReferenceConfiguration): string;

  public function getReferencedEntityType(TableReferenceConfiguration $tableReferenceConfiguration): string;

  public function addWhereConditionValue(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration, EntityInterface $fromEntity): bool;

  public function joinTableConditionColumn(DaViQueryBuilder $queryBuilder, TableReferenceConfiguration $tableReferenceConfiguration): void;
}
