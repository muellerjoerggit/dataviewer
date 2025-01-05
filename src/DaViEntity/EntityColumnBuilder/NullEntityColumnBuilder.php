<?php

namespace App\DaViEntity\EntityColumnBuilder;

use App\Database\DaViQueryBuilder;
use App\DaViEntity\EntityInterface;

class NullEntityColumnBuilder implements EntityColumnBuilderInterface {

  public function buildLabelColumn(DaViQueryBuilder $queryBuilder, string | EntityInterface $entityClass, bool $withEntityLabel = false): void {}

  public function buildEntityKeyColumn(DaViQueryBuilder $queryBuilder, string | EntityInterface $entityClass): void {}

}