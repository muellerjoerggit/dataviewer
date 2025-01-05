<?php

namespace App\DaViEntity\EntityLabel;

use App\DaViEntity\EntityInterface;

interface EntityLabelCrafterInterface {

  public function appendEntityLabelToRows(string | EntityInterface $entityClass, array $rows): array;

}