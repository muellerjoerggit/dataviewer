<?php

namespace App\DaViEntity\ColumnBuilder;

use App\Database\QueryBuilder\DaViQueryBuilder;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class CommonColumnBuilder implements ColumnBuilderInterface {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
  ) {}

  public function buildLabelColumn(QueryBuilderInterface $queryBuilder, string | EntityInterface $entityClass, bool $withEntityLabel = false): void {
    $concat = '';
    $schema = $this->getSchema($entityClass);
    $entityLabelProperties = $schema->getEntityLabelProperties();
    foreach ($entityLabelProperties as $labelProperty) {
      $column = $schema->getColumn($labelProperty);

      if(empty($column)) {
        continue;
      }

      $concat = empty($concat) ? $column : $concat . ',' . $column;
    }
    $concat = $withEntityLabel ? '"' . $schema->getEntityLabel() . ':",' . $concat : $concat;
    $queryBuilder->addSelect('SUBSTRING(CONCAT_WS(" ", ' . $concat . '), 1, 50) AS entityLabel');
  }

  public function buildEntityKeyColumn(QueryBuilderInterface $queryBuilder, string | EntityInterface $entityClass): void {
    $concat = '';
    $client = $queryBuilder->getClient();

    $schema = $this->getSchema($entityClass);
    $uniquePropertyArray = $schema->getFirstUniqueProperties();
    $uniqueProperty = implode('+', $uniquePropertyArray);

    foreach ($uniquePropertyArray as $property) {
      $column = $schema->getColumn($property);
      $concat = empty($concat) ? $column : $concat . ',"+",' . $column;
    }
    $entityType = $schema->getEntityType();
    $uniqueKey = 'CONCAT_WS("", ' . $concat . ')';

    $queryBuilder->addSelect('CONCAT_WS("", "' . $client . '", "::","' . $entityType . '", "::","' . $uniqueProperty . '", "::",' . $concat . ') AS entityKey');
    $queryBuilder->addSelect($uniqueKey . ' AS uniqueKey');
    $queryBuilder->orderBy($uniqueKey);
  }

  protected function getSchema(string | EntityInterface $entityClass): EntitySchema {
    if($entityClass instanceof EntityInterface) {
      $entityClass = $entityClass::class;
    }

    return $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityClass);
  }

}