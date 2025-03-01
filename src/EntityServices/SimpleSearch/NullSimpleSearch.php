<?php

namespace App\EntityServices\SimpleSearch;

use App\DaViEntity\EntityInterface;

class NullSimpleSearch implements SimpleSearchInterface {

  public function getEntityListFromSearchString(string | EntityInterface $entityClass, string $client, string $searchString): array {
    return [];
  }

}