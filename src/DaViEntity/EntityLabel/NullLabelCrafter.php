<?php

namespace App\DaViEntity\EntityLabel;

use App\DaViEntity\EntityInterface;

class NullLabelCrafter implements LabelCrafterInterface {

  public function appendEntityLabelToRows(string | EntityInterface $entityClass, array $rows): array {
    return $rows;
  }

}