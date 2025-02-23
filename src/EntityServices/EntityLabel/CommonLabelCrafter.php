<?php

namespace App\EntityServices\EntityLabel;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class CommonLabelCrafter implements LabelCrafterInterface {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
  ) {}

  public function appendEntityLabelToRows(string | EntityInterface $entityClass, array $rows): array {
    if($entityClass instanceof EntityInterface) {
      $entityClass = $entityClass::class;
    }

    $schema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityClass);
    $labelProperties = $schema->getEntityLabelProperties();

    foreach ($rows as $key => $row) {
      $entityLabel = '';
      foreach ($labelProperties as $labelProperty) {
        if(!isset($row[$labelProperty])) {
          continue;
        }

        $entityLabel .= empty($entityLabel) ? $row[$labelProperty] : ' ' . $row[$labelProperty];
      }
      $rows[$key]['entityLabel'] = $entityLabel;
    }

    return $rows;
  }

  public function getEntityLabel(EntityInterface $entity): string {
    $schema = $entity->getSchema();
    $entityLabelProperties = $schema->getEntityLabelProperties();
    $uniqueProperties = $schema->getUniqueProperties();
    $uniqueProperties = reset($uniqueProperties);
    $label = '';
    foreach ($entityLabelProperties as $property) {
      $value = $entity->getPropertyValueAsString($property);
      $label = empty($label) ? $value : $label . ' ' . $value;
    }

    $unique = '';
    foreach ($uniqueProperties as $property) {
      $value = $entity->getPropertyValueAsString($property);
      $unique = empty($unique) ? $value : $unique . ' ' . $value;
    }

    if(!empty($unique)) {
      $label = $label . ' (' . $unique . ')';
    }

    return $label;
  }

}