<?php

namespace App\Database\TableReference;

use App\Database\DaViQueryBuilder;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttrInterface;
use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('database.table_reference')]
interface TableReferenceHandlerInterface {

  public const string YAML_PARAM_INNER_JOIN = 'innerJoin';
  public const string YAML_PARAM_ENTITY_TYPE = 'entityType';
  public const string YAML_PARAM_CONDITION = 'condition';
  public const string YAML_PARAM_CONDITION_PROPERTIES = 'properties';

  public function joinTable(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration, bool $innerJoin = false, string | null $condition = null): void;

  public function joinTableConditionValue(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration, EntityInterface $fromEntity): void;

  public function getReferencedTableName(TableReferenceAttrInterface $tableReferenceConfiguration): string;

  public function getReferencedEntityType(TableReferenceAttrInterface $tableReferenceConfiguration): string;

  public function addWhereConditionValue(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration, EntityInterface $fromEntity): bool;

  public function joinTableConditionColumn(DaViQueryBuilder $queryBuilder, TableReferenceAttrInterface $tableReferenceConfiguration): void;
}
