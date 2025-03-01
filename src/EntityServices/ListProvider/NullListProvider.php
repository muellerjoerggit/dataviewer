<?php

namespace App\EntityServices\ListProvider;

use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\EntityList;
use App\DaViEntity\EntityInterface;

class NullListProvider implements ListProviderInterface {

  public function getEntityList(string | EntityInterface $entityClass, FilterContainer $filterContainer): EntityList {
    return new EntityList();
  }

}