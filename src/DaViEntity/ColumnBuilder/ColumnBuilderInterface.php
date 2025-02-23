<?php

namespace App\DaViEntity\ColumnBuilder;

use App\Database\DaViQueryBuilder;
use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_column_builder')]
interface ColumnBuilderInterface {

  public function buildLabelColumn(DaViQueryBuilder $queryBuilder, string | EntityInterface $entityClass, bool $withEntityLabel = false): void;

  public function buildEntityKeyColumn(DaViQueryBuilder $queryBuilder, string | EntityInterface $entityClass): void;

}