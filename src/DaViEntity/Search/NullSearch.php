<?php

namespace App\DaViEntity\Search;

use App\DaViEntity\EntityInterface;

class NullSearch implements SearchInterface {

  public function getEntityListFromSearchString(string | EntityInterface $entityClass, string $client, string $searchString): array {
    return [];
  }

}