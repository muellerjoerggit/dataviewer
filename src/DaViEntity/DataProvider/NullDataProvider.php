<?php

namespace App\DaViEntity\DataProvider;

use App\Database\SqlFilter\FilterContainer;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;

class NullDataProvider implements DataProviderInterface {

  public function fetchEntityData(string | EntityInterface $entityClass, FilterContainer $filters, array $options = []): array {
    return [];
  }

}