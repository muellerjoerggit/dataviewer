<?php

namespace App\DaViEntity\ListProvider;

use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\EntityList;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_list_provider')]
interface ListProviderInterface {

  public function getEntityList(string $entityClass, FilterContainer $filterContainer): EntityList;

}