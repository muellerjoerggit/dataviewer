<?php

namespace App\EntityServices\EntityLabel;

use App\DaViEntity\EntityInterface;

class NullLabelCrafter implements LabelCrafterInterface {

  public function appendEntityLabelToRows(string | EntityInterface $entityClass, array $rows): array {
    return $rows;
  }

  public function getEntityLabel(EntityInterface $entity): string {
    return 'Fehler: Null Label';
  }

}