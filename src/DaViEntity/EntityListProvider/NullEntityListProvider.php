<?php

namespace App\DaViEntity\EntityListProvider;

use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\EntityList;
use App\DaViEntity\EntityInterface;

class NullEntityListProvider implements EntityListProviderInterface {

  public function getEntityList(string | EntityInterface $entityClass, FilterContainer $filterContainer): EntityList {
    return new EntityList();
  }

}