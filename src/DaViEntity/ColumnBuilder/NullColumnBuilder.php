<?php

namespace App\DaViEntity\ColumnBuilder;

use App\Database\DaViQueryBuilder;
use App\DaViEntity\EntityInterface;

class NullColumnBuilder implements ColumnBuilderInterface {

  public function buildLabelColumn(DaViQueryBuilder $queryBuilder, string | EntityInterface $entityClass, bool $withEntityLabel = false): void {}

  public function buildEntityKeyColumn(DaViQueryBuilder $queryBuilder, string | EntityInterface $entityClass): void {}

}