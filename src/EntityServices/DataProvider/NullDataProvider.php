<?php

namespace App\EntityServices\DataProvider;

use App\Database\SqlFilter\FilterContainer;
use App\DaViEntity\EntityInterface;

class NullDataProvider implements DataProviderInterface {

  public function fetchEntityData(string | EntityInterface $entityClass, FilterContainer $filters, array $options = []): array {
    return [];
  }

}