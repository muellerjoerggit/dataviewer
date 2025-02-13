<?php

namespace App\DaViEntity\EntityLabel;

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

}