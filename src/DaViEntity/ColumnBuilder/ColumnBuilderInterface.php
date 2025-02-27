<?php

namespace App\DaViEntity\ColumnBuilder;

use App\Database\QueryBuilder\DaViQueryBuilder;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_column_builder')]
interface ColumnBuilderInterface {

  public function buildLabelColumn(QueryBuilderInterface $queryBuilder, string | EntityInterface $entityClass, bool $withEntityLabel = false): void;

  public function buildEntityKeyColumn(QueryBuilderInterface $queryBuilder, string | EntityInterface $entityClass): void;

}