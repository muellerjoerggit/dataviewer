<?php

namespace App\DaViEntity\ListProvider;

use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\EntityList;
use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_list_provider')]
interface EntityListProviderInterface {

  public function getEntityList(string | EntityInterface $entityClass, FilterContainer $filterContainer): EntityList;

}