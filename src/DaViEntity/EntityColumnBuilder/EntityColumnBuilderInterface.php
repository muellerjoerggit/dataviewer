<?php

namespace App\DaViEntity\EntityColumnBuilder;

use App\Database\DaViQueryBuilder;
use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_column_builder')]
interface EntityColumnBuilderInterface {

  public function buildLabelColumn(DaViQueryBuilder $queryBuilder, string | EntityInterface $entityClass, bool $withEntityLabel = false): void;

  public function buildEntityKeyColumn(DaViQueryBuilder $queryBuilder, string | EntityInterface $entityClass): void;

}