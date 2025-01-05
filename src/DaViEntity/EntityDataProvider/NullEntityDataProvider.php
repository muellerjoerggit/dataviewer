<?php

namespace App\DaViEntity\EntityDataProvider;

use App\Database\SqlFilter\FilterContainer;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;

class NullEntityDataProvider implements EntityDataProviderInterface {

  public function fetchEntityData(string | EntityInterface $entityClass, FilterContainer $filters, array $options = []): array {
    return [];
  }

}