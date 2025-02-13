<?php

namespace App\DaViEntity\EntityLabel;

use App\DaViEntity\EntityInterface;

interface LabelCrafterInterface {

  public function appendEntityLabelToRows(string | EntityInterface $entityClass, array $rows): array;

}