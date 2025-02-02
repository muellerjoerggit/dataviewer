<?php

namespace App\DaViEntity\DataProvider;

use App\Database\SqlFilter\FilterContainer;
use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_data_provider')]
interface EntityDataProviderInterface {

  public function fetchEntityData(string | EntityInterface $entityClass, FilterContainer $filters, array $options = []): array;

}