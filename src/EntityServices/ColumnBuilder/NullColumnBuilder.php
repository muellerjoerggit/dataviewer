<?php

namespace App\EntityServices\ColumnBuilder;

use App\Database\QueryBuilder\QueryBuilderInterface;
use App\DaViEntity\EntityInterface;

class NullColumnBuilder implements ColumnBuilderInterface {

  public function buildLabelColumn(QueryBuilderInterface $queryBuilder, string | EntityInterface $entityClass, bool $withEntityLabel = false): void {}

  public function buildEntityKeyColumn(QueryBuilderInterface $queryBuilder, string | EntityInterface $entityClass): void {}

}