<?php

namespace App\DaViEntity\EntityListSearch;

use App\DaViEntity\EntityInterface;

class NullEntityListSearch implements EntityListSearchInterface {

  public function getEntityListFromSearchString(string | EntityInterface $entityClass, string $client, string $searchString): array {
    return [];
  }

}