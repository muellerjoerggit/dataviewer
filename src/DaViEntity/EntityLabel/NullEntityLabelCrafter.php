<?php

namespace App\DaViEntity\EntityLabel;

use App\DaViEntity\EntityInterface;

class NullEntityLabelCrafter implements EntityLabelCrafterInterface {

  public function appendEntityLabelToRows(string | EntityInterface $entityClass, array $rows): array {
    return $rows;
  }

}